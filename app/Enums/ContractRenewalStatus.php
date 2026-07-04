<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum ContractRenewalStatus: string
{
    use HasLocalizedOptions;

    case NotApplicable = 'not_applicable';
    case Pending = 'pending';
    case Renewed = 'renewed';
    case NotRenewed = 'not_renewed';

    public static function translationKey(): string
    {
        return 'contract_renewal_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NotApplicable => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            self::Pending => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::Renewed => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::NotRenewed => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        };
    }
}
