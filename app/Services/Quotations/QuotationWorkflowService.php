<?php

declare(strict_types=1);

namespace App\Services\Quotations;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use DomainException;
use Illuminate\Support\Facades\DB;

final class QuotationWorkflowService
{
    /**
     * @var array<string, list<QuotationStatus>>
     */
    private const TRANSITIONS = [
        'draft' => [
            QuotationStatus::Sent,
            QuotationStatus::Cancelled,
        ],
        'sent' => [
            QuotationStatus::FollowingUp,
            QuotationStatus::Won,
            QuotationStatus::Lost,
            QuotationStatus::Expired,
            QuotationStatus::Cancelled,
        ],
        'following_up' => [
            QuotationStatus::Won,
            QuotationStatus::Lost,
            QuotationStatus::Expired,
            QuotationStatus::Cancelled,
        ],
        'won' => [],
        'lost' => [],
        'expired' => [],
        'cancelled' => [],
    ];

    public function canTransition(Quotation $quotation, QuotationStatus $target): bool
    {
        return in_array($target, self::TRANSITIONS[$quotation->status->value] ?? [], true);
    }

    public function transition(
        Quotation $quotation,
        QuotationStatus $target,
        ?string $reason = null,
    ): Quotation {
        if (! $this->canTransition($quotation, $target)) {
            throw new DomainException(
                "Không thể chuyển báo giá từ {$quotation->status->value} sang {$target->value}.",
            );
        }

        if ($target === QuotationStatus::Sent) {
            $this->ensureReadyToSend($quotation);
        }

        if ($target === QuotationStatus::Lost && blank($reason)) {
            throw new DomainException('Phải nhập lý do khi đánh dấu báo giá không thành công.');
        }

        return DB::transaction(function () use ($quotation, $target, $reason): Quotation {
            $changes = ['status' => $target];

            if ($target === QuotationStatus::Sent) {
                $changes['sent_at'] = now();
            }

            if ($target === QuotationStatus::Won) {
                $changes['won_at'] = now();
            }

            if ($target === QuotationStatus::Lost) {
                $changes['lost_reason'] = trim((string) $reason);
            }

            $quotation->update($changes);

            return $quotation->refresh();
        });
    }

    private function ensureReadyToSend(Quotation $quotation): void
    {
        $quotation->load('services');

        if ($quotation->services->isEmpty()) {
            throw new DomainException('Báo giá phải có ít nhất một dịch vụ trước khi gửi.');
        }

        foreach ($quotation->services as $service) {
            if ($service->service_type->contractType() !== $quotation->contract_type) {
                throw new DomainException('Dịch vụ không thuộc loại hợp đồng của báo giá.');
            }
        }
    }
}
