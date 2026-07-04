<?php

declare(strict_types=1);

namespace App\Services\Quotations;

use App\Enums\ContractStatus;
use App\Enums\QuotationStatus;
use App\Models\Contract;
use App\Models\Quotation;
use DomainException;
use Illuminate\Support\Facades\DB;

final class QuotationToContractService
{
    public function convert(Quotation $quotation, ?string $contractNumber = null, array $customData = []): Contract
    {
        return DB::transaction(function () use ($quotation, $contractNumber, $customData): Contract {
            $lockedQuotation = Quotation::query()
                ->with(['customer', 'services', 'contract'])
                ->lockForUpdate()
                ->findOrFail($quotation->id);

            if ($lockedQuotation->status !== QuotationStatus::Won) {
                throw new DomainException('Chỉ báo giá đã thắng mới được chuyển thành hợp đồng.');
            }

            if ($lockedQuotation->contract !== null) {
                throw new DomainException('Báo giá này đã được chuyển thành hợp đồng.');
            }

            if ($lockedQuotation->services->isEmpty()) {
                throw new DomainException('Báo giá phải có ít nhất một dịch vụ.');
            }

            foreach ($lockedQuotation->services as $service) {
                if ($service->service_type->contractType() !== $lockedQuotation->contract_type) {
                    throw new DomainException('Dịch vụ không thuộc loại hợp đồng của báo giá.');
                }
            }

            $contract = Contract::query()->create([
                'quotation_id' => $lockedQuotation->id,
                'customer_id' => $lockedQuotation->customer_id,
                'owner_id' => $lockedQuotation->owner_id,
                'department_id' => $lockedQuotation->owner?->department_id,
                'contract_number' => $contractNumber ?: ($customData['contract_number'] ?? null),
                'type' => $lockedQuotation->contract_type,
                'status' => ContractStatus::Draft,
                'title' => $customData['title'] ?? ('Hợp đồng - '.$lockedQuotation->customer->name),
                'value' => isset($customData['value']) ? (int) $customData['value'] : ($lockedQuotation->contract_value > 0
                    ? $lockedQuotation->contract_value
                    : $lockedQuotation->total_amount),
                'currency' => $lockedQuotation->currency,
                'payment_method' => $customData['payment_method'] ?? null,
                'signed_at' => $customData['signed_at'] ?? null,
                'starts_at' => $customData['starts_at'] ?? null,
                'ends_at' => $customData['ends_at'] ?? null,
                'notes' => $customData['notes'] ?? null,
            ]);

            foreach ($lockedQuotation->services as $service) {
                $contract->services()->create([
                    'service_type' => $service->service_type,
                    'description' => $service->description,
                    'amount' => $service->total_amount,
                    'sort_order' => $service->sort_order,
                ]);
            }

            $lockedQuotation->update(['converted_at' => now()]);

            return $contract->load(['customer', 'services']);
        });
    }
}
