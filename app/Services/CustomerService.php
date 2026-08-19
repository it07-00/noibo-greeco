<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class CustomerService
{
    /**
     * @return LengthAwarePaginator<Customer>
     */
    public function paginate(
        string $search = '',
        string $type = '',
        string $source = '',
        string $regulatory = '',
        string $caretakerId = '',
        int $perPage = 15
    ): LengthAwarePaginator {
        return Customer::query()
            ->with([
                'caretaker:id,name,email',
                'courses' => fn ($query) => $query->orderBy('starts_at')->orderBy('name'),
            ])
            ->withCount(['quotations', 'contracts'])
            ->when(in_array($type, CustomerType::values(), true), fn ($query) => $query->where('type', $type))
            ->when($source !== '', fn (Builder $query) => $query->where('system_source', $source))
            ->when($caretakerId !== '', function (Builder $query) use ($caretakerId): void {
                if ($caretakerId === 'unassigned') {
                    $query->whereNull('caretaker_id')
                        ->where(function (Builder $q): void {
                            $q->whereNull('caretaker_name')
                                ->orWhere('caretaker_name', '');
                        });
                } elseif (str_starts_with($caretakerId, 'name:')) {
                    $name = substr($caretakerId, 5);
                    $query->where(function (Builder $q) use ($name): void {
                        $q->where('caretaker_name', $name)
                            ->orWhereHas('caretaker', fn ($uq) => $uq->where('name', $name));
                    });
                } elseif (is_numeric($caretakerId)) {
                    $id = (int) $caretakerId;
                    $userName = User::where('id', $id)->value('name');
                    $query->where(function (Builder $q) use ($id, $userName): void {
                        $q->where('caretaker_id', $id);
                        if ($userName) {
                            $q->orWhere('caretaker_name', $userName);
                        }
                    });
                }
            })
            ->when($regulatory !== '', function (Builder $query) use ($regulatory): void {
                if ($regulatory === 'ghg_inventory') {
                    $query->where('is_ghg_inventory', true);
                } elseif ($regulatory === 'energy_audit') {
                    $query->where('is_energy_audit', true);
                } elseif ($regulatory === 'regular') {
                    $query->where('is_ghg_inventory', false)->where('is_energy_audit', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('tax_code', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%")
                        ->orWhere('industry', 'like', "%{$search}%")
                        ->orWhere('caretaker_name', 'like', "%{$search}%")
                        ->orWhereHas('caretaker', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data, ?Customer $customer = null): Customer
    {
        if ($customer === null) {
            return Customer::query()->create($data);
        }

        $customer->update($data);

        return $customer->refresh();
    }
}