<?php

declare(strict_types=1);

namespace App\Enums;

enum MarketingChannel: string
{
    case Facebook = 'facebook';
    case Zalo = 'zalo';
    case Website = 'website';
    case TikTok = 'tiktok';
    case Youtube = 'youtube';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::Zalo => 'Zalo',
            self::Website => 'Website',
            self::TikTok => 'TikTok',
            self::Youtube => 'YouTube',
            self::Other => 'Khác',
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::Facebook => 'fi fi-brands-facebook text-primary',
            self::Zalo => 'fi fi-rr-comment-alt text-info',
            self::Website => 'fi fi-rr-globe text-success',
            self::TikTok => 'fi fi-brands-tiktok text-dark',
            self::Youtube => 'fi fi-brands-youtube text-danger',
            self::Other => 'fi fi-rr-share text-secondary',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Facebook => 'bg-primary-subtle text-primary border-primary',
            self::Zalo => 'bg-info-subtle text-info border-info',
            self::Website => 'bg-success-subtle text-success border-success',
            self::TikTok => 'bg-dark-subtle text-dark border-dark',
            self::Youtube => 'bg-danger-subtle text-danger border-danger',
            self::Other => 'bg-secondary-subtle text-secondary border-secondary',
        };
    }
}
