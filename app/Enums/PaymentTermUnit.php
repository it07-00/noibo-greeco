<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum PaymentTermUnit: string
{
    use HasLocalizedOptions;

    case CalendarDays = 'calendar_days';
    case BusinessDays = 'business_days';

    public static function translationKey(): string
    {
        return 'payment_term_unit';
    }
}
