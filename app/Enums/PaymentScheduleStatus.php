<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum PaymentScheduleStatus: string
{
    use HasLocalizedOptions;

    case WaitingCondition = 'waiting_condition';
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public static function translationKey(): string
    {
        return 'payment_schedule_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::WaitingCondition => 'bg-secondary-subtle text-secondary',
            self::Pending => 'bg-info-subtle text-info',
            self::PartiallyPaid => 'bg-warning-subtle text-warning',
            self::Paid => 'bg-success-subtle text-success',
            self::Overdue => 'bg-danger-subtle text-danger',
            self::Cancelled => 'bg-dark-subtle text-dark',
        };
    }
}
