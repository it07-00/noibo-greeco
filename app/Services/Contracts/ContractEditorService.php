<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class ContractEditorService
{
    public function save(?Contract $contract, array $data, array $services, string $newCustomerName, User $actor): Contract
    {
        Gate::forUser($actor)->authorize($contract ? 'update' : 'create', $contract ?? Contract::class);

        return DB::transaction(function () use ($contract, $data, $services, $newCustomerName, $actor): Contract {
            $target = $contract ? Contract::whereKey($contract->id)->lockForUpdate()->firstOrFail() : new Contract;
            Gate::forUser($actor)->authorize($target->exists ? 'update' : 'create', $target->exists ? $target : Contract::class);
            if ($actor->hasRole(RoleEnum::Consultant->value)) {
                $data['value'] = $target->exists ? $target->value : 0;
            }
            if ($target->exists && (int) $data['value'] !== (int) $target->value && $target->paymentSchedules()->exists()
                && (int) $data['value'] !== (int) $target->paymentSchedules()->sum('amount')) {
                throw ValidationException::withMessages(['form.value' => 'Giá trị hợp đồng phải khớp tổng các đợt thanh toán đang có.']);
            }
            foreach ($services as $service) {
                if (ServiceType::from($service['service_type'])->contractType()->value !== $data['type']) {
                    throw ValidationException::withMessages(['serviceRows' => 'Dịch vụ phải thuộc loại hợp đồng đã chọn.']);
                }
            }
            if (! empty($data['is_renewal']) && $target->exists && (int) ($data['parent_contract_id'] ?? 0) === $target->id) {
                throw ValidationException::withMessages(['form.parent_contract_id' => 'Hợp đồng không thể tái ký chính nó.']);
            }
            if (! empty($data['is_renewal']) && ! empty($data['parent_contract_id'])) {
                $parent = Contract::findOrFail($data['parent_contract_id']);
                Gate::forUser($actor)->authorize('view', $parent);
                $visited = [];
                while ($parent) {
                    if (in_array($parent->id, $visited, true) || ($target->exists && $parent->id === $target->id)) {
                        throw ValidationException::withMessages(['form.parent_contract_id' => 'Chuỗi hợp đồng tái ký không được tạo vòng lặp.']);
                    }
                    $visited[] = $parent->id;
                    $parent = $parent->parent_contract_id ? Contract::find($parent->parent_contract_id) : null;
                }
            }
            if (empty($data['customer_id'])) {
                Gate::forUser($actor)->authorize('create', Customer::class);
                $data['customer_id'] = Customer::create(['name' => trim($newCustomerName)])->id;
            }
            if (! $actor->can(PermissionEnum::ContractViewAll->value)) {
                $data['owner_id'] = $target->exists ? $target->owner_id : $actor->id;
            }
            if ($actor->hasRole(RoleEnum::Consultant->value)) {
                foreach (['value', 'original_amount', 'customer_commission', 'commission', 'commission_tax', 'revenue', 'payment_percentage'] as $field) {
                    unset($data[$field]);
                }
                if (! $target->exists) {
                    $data['value'] = 0;
                }
            } else {
                $data['commission'] = $data['customer_commission'] ?? 0;
            }
            // Kế toán quản lý chi NCC và số nội bộ ở luồng dòng tiền; form nghiệp vụ giữ nguyên dữ liệu đó.
            unset($data['ncc_payment'], $data['ncc_payment_status'], $data['ncc_payment_sheet_url'], $data['shd_greeco']);
            $data['parent_contract_id'] = ! empty($data['is_renewal']) ? ($data['parent_contract_id'] ?? null) : null;
            $target->fill($data)->save();
            $existingIds = [];
            foreach ($services as $index => $row) {
                $item = isset($row['id']) ? $target->services()->findOrFail($row['id']) : $target->services()->make();
                $attributes = ['service_type' => $row['service_type'], 'description' => $row['description'] ?: null, 'sort_order' => $index];
                if (! $actor->hasRole(RoleEnum::Consultant->value) || ! $item->exists) {
                    $attributes['amount'] = $actor->hasRole(RoleEnum::Consultant->value) ? 0 : $row['amount'];
                }
                $item->fill($attributes)->save();
                $existingIds[] = $item->id;
            }
            $target->services()->whereNotIn('id', $existingIds)->delete();

            return $target->refresh();
        });
    }
}
