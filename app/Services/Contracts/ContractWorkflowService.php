<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ContractStatus;
use App\Enums\PaymentScheduleStatus;
use App\Models\Contract;
use App\Services\Payments\PaymentPlanValidator;
use DomainException;
use Illuminate\Support\Facades\DB;

final class ContractWorkflowService
{
    /**
     * @var array<string, list<ContractStatus>>
     */
    private const TRANSITIONS = [
        'draft' => [
            ContractStatus::InternalReview,
            ContractStatus::Cancelled,
        ],
        'internal_review' => [
            ContractStatus::Draft,
            ContractStatus::WaitingCustomerSignature,
            ContractStatus::Cancelled,
        ],
        'waiting_customer_signature' => [
            ContractStatus::InternalReview,
            ContractStatus::Active,
            ContractStatus::Cancelled,
        ],
        'active' => [
            ContractStatus::Suspended,
            ContractStatus::Completed,
            ContractStatus::Cancelled,
        ],
        'suspended' => [
            ContractStatus::Active,
            ContractStatus::Cancelled,
        ],
        'completed' => [
            ContractStatus::Liquidated,
        ],
        'liquidated' => [],
        'cancelled' => [],
    ];

    public function __construct(
        private readonly PaymentPlanValidator $paymentPlanValidator,
    ) {}

    public function canTransition(Contract $contract, ContractStatus $target): bool
    {
        return in_array($target, self::TRANSITIONS[$contract->status->value] ?? [], true);
    }

    public function transition(
        Contract $contract,
        ContractStatus $target,
        ?string $reason = null,
    ): Contract {
        if (! $this->canTransition($contract, $target)) {
            throw new DomainException(
                "Không thể chuyển hợp đồng từ {$contract->status->value} sang {$target->value}.",
            );
        }

        if ($target === ContractStatus::InternalReview) {
            $this->ensureReadyForReview($contract);
        }

        if ($target === ContractStatus::Active && $contract->signed_at === null) {
            throw new DomainException('Phải có ngày ký trước khi kích hoạt hợp đồng.');
        }

        if ($target === ContractStatus::Suspended && blank($reason)) {
            throw new DomainException('Phải nhập lý do tạm dừng hợp đồng.');
        }

        if ($target === ContractStatus::Cancelled && blank($reason)) {
            throw new DomainException('Phải nhập lý do hủy hợp đồng.');
        }

        if ($target === ContractStatus::Liquidated) {
            $this->ensureFinanciallySettled($contract);
        }

        return DB::transaction(function () use ($contract, $target, $reason): Contract {
            $changes = ['status' => $target];

            if ($target === ContractStatus::Suspended) {
                $changes['suspension_reason'] = trim((string) $reason);
            }

            if ($target === ContractStatus::Cancelled) {
                $changes['cancellation_reason'] = trim((string) $reason);
            }

            if ($target === ContractStatus::Completed) {
                $changes['completed_at'] = now();
            }

            if ($target === ContractStatus::Liquidated) {
                $changes['liquidated_at'] = now();
            }

            $contract->update($changes);

            return $contract->refresh();
        });
    }

    private function ensureReadyForReview(Contract $contract): void
    {
        $contract->load('services');

        if ($contract->services->isEmpty()) {
            throw new DomainException('Hợp đồng phải có ít nhất một dịch vụ.');
        }

        foreach ($contract->services as $service) {
            if ($service->service_type->contractType() !== $contract->type) {
                throw new DomainException('Dịch vụ không thuộc loại hợp đồng.');
            }
        }

        $this->paymentPlanValidator->validate($contract);
    }

    private function ensureFinanciallySettled(Contract $contract): void
    {
        $hasOutstandingSchedule = $contract->paymentSchedules()
            ->whereNull('cancelled_at')
            ->where('status', '!=', PaymentScheduleStatus::Paid->value)
            ->exists();

        if ($hasOutstandingSchedule) {
            throw new DomainException('Không thể thanh lý khi hợp đồng chưa thu đủ tiền.');
        }
    }
}
