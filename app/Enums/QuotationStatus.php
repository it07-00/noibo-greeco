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
            self::Draft => 'bg-secondary-subtle text-secondary',
            self::Sent => 'bg-info-subtle text-info',
            self::FollowingUp => 'bg-warning-subtle text-warning',
            self::Won => 'bg-success-subtle text-success',
            self::Lost => 'bg-danger-subtle text-danger',
            self::Expired => 'bg-dark-subtle text-dark',
            self::Cancelled => 'bg-secondary-subtle text-secondary',
        };
    }
}
