<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Enums\PaymentConditionType;
use App\Enums\PaymentHandoverStatus;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use App\Models\User;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final class PaymentScheduleService
{
    public function __construct(
        private readonly PaymentPlanValidator $planValidator,
        private readonly PaymentDueDateService $dueDateService,
        private readonly PaymentScheduleStatusResolver $statusResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(
        Contract $contract,
        array $data,
        ?ContractPaymentSchedule $schedule = null,
    ): ContractPaymentSchedule {
        if ($schedule !== null && $schedule->contract_id !== $contract->id) {
            throw new DomainException('Đợt thanh toán không thuộc hợp đồng này.');
        }

        if ($schedule?->confirmed_at !== null) {
            throw new DomainException('Lịch đã được Kế toán xác nhận, không thể sửa trực tiếp.');
        }

        if ($schedule !== null && $schedule->allocations()->exists()) {
            throw new DomainException('Đợt đã phát sinh thanh toán, không thể sửa trực tiếp.');
        }

        if ((float) $data['amount'] <= 0) {
            throw new DomainException('Số tiền của đợt phải lớn hơn 0.');
        }

        $conditionType = PaymentConditionType::from((string) $data['condition_type']);

        if ($conditionType === PaymentConditionType::Custom && blank($data['custom_condition'] ?? null)) {
            throw new DomainException('Vui lòng nhập điều kiện thanh toán tùy chỉnh.');
        }

        $savedSchedule = DB::transaction(function () use ($contract, $data, $schedule): ContractPaymentSchedule {
            $payload = array_merge($data, [
                'contract_id' => $contract->id,
                'handover_status' => PaymentHandoverStatus::BusinessPreparing,
                'confirmed_at' => null,
                'confirmed_by' => null,
            ]);

            if ($schedule === null) {
                $payload['installment_number'] = ((int) $contract->paymentSchedules()->max('installment_number')) + 1;

                return ContractPaymentSchedule::query()->create($payload);
            }

            $schedule->update($payload);

            return $schedule->refresh();
        });

        return $this->statusResolver->refresh($savedSchedule);
    }

    public function delete(ContractPaymentSchedule $schedule): void
    {
        if ($schedule->confirmed_at !== null || $schedule->allocations()->exists()) {
            throw new DomainException('Không thể xóa đợt đã xác nhận hoặc đã phát sinh tiền.');
        }

        $schedule->delete();
    }

    public function submitPlan(Contract $contract, ?int $accountingDepartmentId = null): void
    {
        $this->planValidator->validate($contract);

        DB::transaction(function () use ($contract, $accountingDepartmentId): void {
            $contract->paymentSchedules()
                ->whereNull('cancelled_at')
                ->update([
                    'handover_status' => PaymentHandoverStatus::SubmittedToAccounting->value,
                    'responsible_department_id' => $accountingDepartmentId,
                    'next_action' => 'Kiểm tra và xác nhận lịch thanh toán',
                ]);
        });
    }

    public function confirmPlan(Contract $contract, User $actor): void
    {
        $this->planValidator->validate($contract);

        $hasUnsubmittedSchedule = $contract->paymentSchedules()
            ->whereNull('cancelled_at')
            ->whereNotIn('handover_status', [
                PaymentHandoverStatus::SubmittedToAccounting->value,
                PaymentHandoverStatus::AccountingReviewing->value,
            ])
            ->exists();

        if ($hasUnsubmittedSchedule) {
            throw new DomainException('Kinh doanh chưa gửi đầy đủ kế hoạch thanh toán sang Kế toán.');
        }

        DB::transaction(function () use ($contract, $actor): void {
            $contract->paymentSchedules()
                ->whereNull('cancelled_at')
                ->update([
                    'handover_status' => PaymentHandoverStatus::WaitingForCustomerPayment->value,
                    'confirmed_at' => now(),
                    'confirmed_by' => $actor->id,
                    'responsible_department_id' => $actor->department_id,
                    'responsible_user_id' => $actor->id,
                    'next_action' => 'Theo dõi tiền về',
                ]);
        });
    }

    public function returnPlan(Contract $contract, string $reason): void
    {
        if (blank($reason)) {
            throw new DomainException('Phải nhập lý do trả lại kế hoạch thanh toán.');
        }

        $hasSubmittedSchedule = $contract->paymentSchedules()
            ->whereNull('cancelled_at')
            ->whereIn('handover_status', [
                PaymentHandoverStatus::SubmittedToAccounting->value,
                PaymentHandoverStatus::AccountingReviewing->value,
            ])
            ->exists();

        if (! $hasSubmittedSchedule) {
            throw new DomainException('Không có kế hoạch nào đang chờ Kế toán kiểm tra.');
        }

        $contract->paymentSchedules()
            ->whereNull('cancelled_at')
            ->update([
                'handover_status' => PaymentHandoverStatus::ReturnedToBusiness->value,
                'responsible_department_id' => $contract->department_id,
                'responsible_user_id' => $contract->owner_id,
                'next_action' => trim($reason),
                'confirmed_at' => null,
                'confirmed_by' => null,
            ]);
    }

    public function trigger(
        ContractPaymentSchedule $schedule,
        CarbonInterface $triggeredAt,
    ): ContractPaymentSchedule {
        $schedule = $this->dueDateService->trigger($schedule, $triggeredAt);

        return $this->statusResolver->refresh($schedule);
    }
}
