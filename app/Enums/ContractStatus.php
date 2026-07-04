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
            self::Draft => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            self::InternalReview => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::WaitingCustomerSignature => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::Active => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            self::Suspended => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::Completed => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::Liquidated => 'bg-success text-white',
            self::Cancelled => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        };
    }
}
