<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Enums\PaymentTermUnit;
use App\Models\ContractPaymentSchedule;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class PaymentDueDateService
{
    public function trigger(
        ContractPaymentSchedule $schedule,
        CarbonInterface $triggeredAt,
    ): ContractPaymentSchedule {
        $triggerDate = CarbonImmutable::instance($triggeredAt);
        $dueDate = $schedule->due_date;

        if ($schedule->payment_term_days !== null) {
            $dueDate = $schedule->payment_term_unit === PaymentTermUnit::BusinessDays
                ? $triggerDate->addWeekdays($schedule->payment_term_days)
                : $triggerDate->addDays($schedule->payment_term_days);
        }

        $schedule->update([
            'triggered_at' => $triggerDate->toDateString(),
            'due_date' => $dueDate?->toDateString(),
        ]);

        return $schedule->refresh();
    }
}
