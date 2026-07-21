<?php

declare(strict_types=1);

namespace App\Enums;

enum MarketingPlanStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bản nháp',
            self::Pending => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary-subtle text-secondary border-secondary',
            self::Pending => 'bg-warning-subtle text-warning border-warning',
            self::Approved => 'bg-success-subtle text-success border-success',
            self::Rejected => 'bg-danger-subtle text-danger border-danger',
        };
    }

    public function calendarClass(): array
    {
        return match ($this) {
            self::Draft => ['bg-secondary-subtle', 'text-secondary', 'border-secondary', 'p-1', 'fw-semibold'],
            self::Pending => ['bg-warning-subtle', 'text-warning', 'border-warning', 'p-1', 'fw-semibold'],
            self::Approved => ['bg-success-subtle', 'text-success', 'border-success', 'p-1', 'fw-semibold'],
            self::Rejected => ['bg-danger-subtle', 'text-danger', 'border-danger', 'p-1', 'fw-semibold'],
        };
    }
}
