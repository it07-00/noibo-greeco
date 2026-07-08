<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Enums\PaymentScheduleStatus;
use App\Models\ContractPaymentSchedule;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class PaymentScheduleStatusResolver
{
    public function resolve(
        ContractPaymentSchedule $schedule,
        ?CarbonInterface $today = null,
    ): PaymentScheduleStatus {
        if ($schedule->cancelled_at !== null) {
            return PaymentScheduleStatus::Cancelled;
        }

        $allocatedAmount = (float) $schedule->allocations()
            ->whereHas('payment', static fn ($query) => $query->whereNull('voided_at'))
            ->sum('allocated_amount');

        if ($allocatedAmount >= $schedule->amount) {
            return PaymentScheduleStatus::Paid;
        }

        $currentDate = CarbonImmutable::instance($today ?? now())->startOfDay();

        if ($schedule->due_date !== null && $schedule->due_date->lt($currentDate)) {
            return PaymentScheduleStatus::Overdue;
        }

        if ($allocatedAmount > 0) {
            return PaymentScheduleStatus::PartiallyPaid;
        }

        if ($schedule->triggered_at === null && $schedule->due_date === null) {
            return PaymentScheduleStatus::WaitingCondition;
        }

        return PaymentScheduleStatus::Pending;
    }

    public function refresh(ContractPaymentSchedule $schedule): ContractPaymentSchedule
    {
        $status = $this->resolve($schedule);

        if ($schedule->status !== $status) {
            $schedule->update(['status' => $status]);
        }

        return $schedule->refresh();
    }
}
