<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum ContractStatus: string
{
    use HasLocalizedOptions;

    case Draft = 'draft';
    case InternalReview = 'internal_review';
    case WaitingCustomerSignature = 'waiting_customer_signature';
    case Active = 'active';
    case Suspended = 'suspended';
    case Completed = 'completed';
    case Liquidated = 'liquidated';
    case Cancelled = 'cancelled';

    public static function translationKey(): string
    {
        return 'contract_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary-subtle text-secondary',
            self::InternalReview => 'bg-info-subtle text-info',
            self::WaitingCustomerSignature => 'bg-warning-subtle text-warning',
            self::Active => 'bg-primary-subtle text-primary',
            self::Suspended => 'bg-warning-subtle text-warning',
            self::Completed => 'bg-success-subtle text-success',
            self::Liquidated => 'bg-success text-white',
            self::Cancelled => 'bg-danger-subtle text-danger',
        };
    }
}
