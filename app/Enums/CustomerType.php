<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum CustomerType: string
{
    use HasLocalizedOptions;

    case Organization = 'organization';
    case Individual = 'individual';

    public static function translationKey(): string
    {
        return 'customer_type';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Organization => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            self::Individual => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        };
    }
}
