<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Enums\PaymentHandoverStatus;
use App\Enums\PaymentScheduleStatus;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

final class PaymentRecordingService
{
    public function __construct(
        private readonly PaymentAllocationService $allocationService,
    ) {}

    /**
     * @param  array<string, mixed>  $paymentData
     * @param  list<array{payment_schedule_id: int, amount: int}>  $allocations
     */
    public function record(
        Contract $contract,
        array $paymentData,
        array $allocations,
        User $actor,
    ): ContractPayment {
        $amount = (int) $paymentData['amount'];

        if ($amount <= 0) {
            throw new DomainException('Số tiền nhận phải lớn hơn 0.');
        }

        $allocatedTotal = array_sum(array_column($allocations, 'amount'));

        if ($allocatedTotal > $amount) {
            throw new DomainException('Tổng tiền phân bổ vượt quá số tiền thực nhận.');
        }

        return DB::transaction(function () use (
            $contract,
            $paymentData,
            $allocations,
            $actor,
            $amount,
        ): ContractPayment {
            $payment = ContractPayment::query()->create(array_merge($paymentData, [
                'contract_id' => $contract->id,
                'amount' => $amount,
                'recorded_by' => $actor->id,
            ]));

            foreach ($allocations as $allocation) {
                if ((int) $allocation['amount'] <= 0) {
                    continue;
                }

                $schedule = ContractPaymentSchedule::query()
                    ->where('contract_id', $contract->id)
                    ->findOrFail((int) $allocation['payment_schedule_id']);

                $this->allocationService->allocate($payment, $schedule, (int) $allocation['amount']);

                if ($schedule->refresh()->status === PaymentScheduleStatus::Paid) {
                    $schedule->update([
                        'handover_status' => PaymentHandoverStatus::Completed,
                        'next_action' => null,
                        'next_action_due_at' => null,
                    ]);
                }
            }

            return $payment->load(['allocations.paymentSchedule', 'recorder']);
        });
    }
}
