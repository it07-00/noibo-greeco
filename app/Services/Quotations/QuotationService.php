<?php

declare(strict_types=1);

namespace App\Services\Quotations;

use App\Enums\ContractType;
use App\Enums\QuotationStatus;
use App\Enums\ServiceType;
use App\Models\Quotation;
use App\Models\User;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class QuotationService
{
    public function __construct(
        private readonly QuotationVersionService $versionService,
    ) {}

    /**
     * @return LengthAwarePaginator<Quotation>
     */
    public function paginate(
        string $search = '',
        string $status = '',
        string $contractType = '',
        int $perPage = 15,
    ): LengthAwarePaginator {
        return Quotation::query()
            ->with(['customer', 'owner', 'contract', 'services'])
            ->withCount('services')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('quotation_number', 'like', "%{$search}%")
                        ->orWhereHas(
                            'customer',
                            static fn ($customerQuery) => $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('tax_code', 'like', "%{$search}%"),
                        );
                });
            })
            ->when($status !== '', static fn ($query) => $query->where('status', $status))
            ->when($contractType !== '', static fn ($query) => $query->where('contract_type', $contractType))
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * @return array{total: int, following_up: int, won: int, pipeline_value: int}
     */
    public function summary(): array
    {
        return [
            'total' => Quotation::query()->count(),
            'following_up' => Quotation::query()
                ->whereIn('status', [
                    QuotationStatus::Sent->value,
                    QuotationStatus::FollowingUp->value,
                ])
                ->count(),
            'won' => Quotation::query()->where('status', QuotationStatus::Won->value)->count(),
            'pipeline_value' => (float) Quotation::query()
                ->whereIn('status', [
                    QuotationStatus::Draft->value,
                    QuotationStatus::Sent->value,
                    QuotationStatus::FollowingUp->value,
                ])
                ->sum('contract_value'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>  $services
     */
    public function saveDraft(
        array $data,
        array $services,
        User $actor,
        ?Quotation $quotation = null,
    ): Quotation {
        $contractType = ContractType::from((string) $data['contract_type']);
        $normalizedServices = $this->normalizeServices($services, $contractType);

        if ($quotation !== null && in_array($quotation->status, [
            QuotationStatus::Won,
            QuotationStatus::Lost,
            QuotationStatus::Expired,
            QuotationStatus::Cancelled,
        ], true)) {
            throw new DomainException('Không thể sửa báo giá đã kết thúc.');
        }

        return DB::transaction(function () use (
            $data,
            $normalizedServices,
            $actor,
            $quotation,
        ): Quotation {
            if ($quotation !== null && $quotation->status !== QuotationStatus::Draft) {
                $this->versionService->capture(
                    $quotation,
                    $actor,
                    'Phiên bản trước khi điều chỉnh',
                );
            }

            $totalAmount = array_sum(array_column($normalizedServices, 'total_amount'));
            $quotationData = array_merge($data, [
                'owner_id' => $data['owner_id'] ?? $quotation?->owner_id ?? $actor->id,
                'total_amount' => $totalAmount,
                'original_amount' => $data['original_amount'] ?? $totalAmount,
                'contract_value' => $data['contract_value'] ?? $totalAmount,
            ]);

            if ($quotation === null) {
                $quotation = Quotation::query()->create(array_merge($quotationData, [
                    'status' => QuotationStatus::Draft,
                ]));
                $quotation->update([
                    'quotation_number' => sprintf(
                        'BG-%s-%04d',
                        now()->format('Y'),
                        $quotation->id,
                    ),
                ]);
            } else {
                $quotation->update($quotationData);
                $quotation->services()->delete();
            }

            foreach ($normalizedServices as $index => $service) {
                $quotation->services()->create(array_merge($service, [
                    'sort_order' => $index,
                ]));
            }

            return $quotation->refresh()->load(['customer', 'owner', 'services']);
        });
    }

    /**
     * @param  list<array<string, mixed>>  $services
     * @return list<array<string, mixed>>
     */
    private function normalizeServices(array $services, ContractType $contractType): array
    {
        if ($services === []) {
            throw new DomainException('Báo giá phải có ít nhất một dịch vụ.');
        }

        $normalized = [];

        foreach ($services as $service) {
            $serviceType = ServiceType::from((string) $service['service_type']);

            if ($serviceType->contractType() !== $contractType) {
                throw new DomainException('Dịch vụ không thuộc loại hợp đồng đã chọn.');
            }

            $quantity = (float) $service['quantity'];
            $unitPrice = (float) $service['unit_price'];

            if ($quantity <= 0 || $unitPrice < 0) {
                throw new DomainException('Số lượng và đơn giá dịch vụ không hợp lệ.');
            }

            $normalized[] = [
                'service_type' => $serviceType,
                'description' => blank($service['description'] ?? null)
                    ? null
                    : trim((string) $service['description']),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => (float) round($quantity * $unitPrice),
            ];
        }

        return $normalized;
    }
}
