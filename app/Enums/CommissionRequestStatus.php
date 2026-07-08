<?php

declare(strict_types=1);

namespace App\Enums;

enum CommissionRequestStatus: string
{
    case Estimated = 'estimated';
    case Approved = 'approved';
    case Paid = 'paid';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Estimated => 'Dự chi',
            self::Approved => 'Đã duyệt',
            self::Paid => 'Đã chi',
            self::Rejected => 'Từ chối',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Estimated => 'bg-secondary-subtle text-secondary',
            self::Approved => 'bg-warning-subtle text-warning',
            self::Paid => 'bg-success-subtle text-success',
            self::Rejected => 'bg-danger-subtle text-danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
