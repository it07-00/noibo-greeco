<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum QuotationStatus: string
{
    use HasLocalizedOptions;

    case Draft = 'draft';
    case Sent = 'sent';
    case FollowingUp = 'following_up';
    case Won = 'won';
    case Lost = 'lost';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public static function translationKey(): string
    {
        return 'quotation_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            self::Sent => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::FollowingUp => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::Won => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::Lost => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            self::Expired => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
            self::Cancelled => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        };
    }
}
