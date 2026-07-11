<?php

declare(strict_types=1);

namespace App\Enums;

enum SupportRequestStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xử lý',
            self::InProgress => 'Đang hỗ trợ',
            self::Resolved => 'Đã giải quyết',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning-subtle text-warning-emphasis',
            self::InProgress => 'bg-primary-subtle text-primary-emphasis',
            self::Resolved => 'bg-success-subtle text-success-emphasis',
        };
    }
}
