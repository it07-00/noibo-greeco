<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum PaymentMethod: string
{
    use HasLocalizedOptions;

    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case Offset = 'offset';
    case Other = 'other';

    public static function translationKey(): string
    {
        return 'payment_method';
    }
}
