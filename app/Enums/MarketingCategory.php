<?php

declare(strict_types=1);

namespace App\Enums;

enum MarketingCategory: string
{
    case Website = 'website';
    case Press = 'press';
    case Internal = 'internal';
    case Newsletter = 'newsletter';
    case Event = 'event';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Bài viết Website',
            self::Press => 'Thông cáo báo chí',
            self::Internal => 'Truyền thông nội bộ',
            self::Newsletter => 'Bản tin / Newsletter',
            self::Event => 'Kế hoạch sự kiện',
            self::Other => 'Khác',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Website => 'bg-info-subtle text-info border-info',
            self::Press => 'bg-primary-subtle text-primary border-primary',
            self::Internal => 'bg-success-subtle text-success border-success',
            self::Newsletter => 'bg-warning-subtle text-warning border-warning',
            self::Event => 'bg-purple-subtle text-purple border-purple',
            self::Other => 'bg-secondary-subtle text-secondary border-secondary',
        };
    }
}
