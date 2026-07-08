<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\ContractPayment;
use App\Models\ContractPaymentAllocation;
use App\Models\ContractPaymentSchedule;
use DomainException;
use Illuminate\Support\Facades\DB;

final class PaymentAllocationService
{
    public function __construct(
        private readonly PaymentScheduleStatusResolver $statusResolver,
    ) {}

    public function allocate(
        ContractPayment $payment,
        ContractPaymentSchedule $schedule,
        float $amount,
    ): ContractPaymentAllocation {
        if ($amount <= 0.0) {
            throw new DomainException('Số tiền phân bổ phải lớn hơn 0.');
        }

        $allocation = DB::transaction(function () use ($payment, $schedule, $amount): ContractPaymentAllocation {
            $lockedPayment = ContractPayment::query()->lockForUpdate()->findOrFail($payment->id);
            $lockedSchedule = ContractPaymentSchedule::query()->lockForUpdate()->findOrFail($schedule->id);

            if ($lockedPayment->contract_id !== $lockedSchedule->contract_id) {
                throw new DomainException('Không thể phân bổ tiền cho đợt của hợp đồng khác.');
            }

            if ($lockedPayment->voided_at !== null) {
                throw new DomainException('Không thể phân bổ một giao dịch đã hủy.');
            }

            if ($lockedSchedule->cancelled_at !== null) {
                throw new DomainException('Không thể phân bổ tiền cho đợt đã hủy.');
            }

            $existingAllocation = ContractPaymentAllocation::query()
                ->where('payment_id', $lockedPayment->id)
                ->where('payment_schedule_id', $lockedSchedule->id)
                ->first();

            $paymentAllocatedElsewhere = (float) $lockedPayment->allocations()
                ->when(
                    $existingAllocation,
                    static fn ($query) => $query->whereKeyNot($existingAllocation->id),
                )
                ->sum('allocated_amount');

            if ($paymentAllocatedElsewhere + $amount > $lockedPayment->amount) {
                throw new DomainException('Tổng tiền phân bổ vượt quá giá trị giao dịch.');
            }

            $scheduleAllocatedElsewhere = (float) $lockedSchedule->allocations()
                ->whereHas('payment', static fn ($query) => $query->whereNull('voided_at'))
                ->when(
                    $existingAllocation,
                    static fn ($query) => $query->whereKeyNot($existingAllocation->id),
                )
                ->sum('allocated_amount');

            if ($scheduleAllocatedElsewhere + $amount > $lockedSchedule->amount) {
                throw new DomainException('Số tiền phân bổ vượt quá số phải thu của đợt.');
            }

            if ($existingAllocation !== null) {
                $existingAllocation->update(['allocated_amount' => $amount]);

                return $existingAllocation->refresh();
            }

            return ContractPaymentAllocation::query()->create([
                'payment_id' => $lockedPayment->id,
                'payment_schedule_id' => $lockedSchedule->id,
                'allocated_amount' => $amount,
            ]);
        });

        $this->statusResolver->refresh($schedule);

        return $allocation;
    }

    public function unallocatedAmount(ContractPayment $payment): int|float
    {
        $val = max(
            0.0,
            $payment->amount - (float) $payment->allocations()->sum('allocated_amount'),
        );

        return $val > PHP_INT_MAX ? (float) $val : (int) $val;
    }
}
