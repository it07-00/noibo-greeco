<?php

declare(strict_types=1);

namespace App\Enums;

enum CustomerCareStatus: string
{
    case NOT_CONTACTED = 'not_contacted';
    case CONTACTED = 'contacted';
    case IN_PROGRESS = 'in_progress';
    case SIGNED = 'signed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NOT_CONTACTED => 'Chưa liên hệ',
            self::CONTACTED => 'Đã liên hệ',
            self::IN_PROGRESS => 'Đang đàm phán',
            self::SIGNED => 'Đã ký hợp đồng',
            self::REJECTED => 'Từ chối dịch vụ',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NOT_CONTACTED => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            self::CONTACTED => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::IN_PROGRESS => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::SIGNED => 'bg-success-subtle text-success border border-success-subtle',
            self::REJECTED => 'bg-danger-subtle text-danger border border-danger-subtle',
        };
    }

    public static function labelFor(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $case = self::tryFrom($value);

        return $case ? $case->label() : $value;
    }

    public static function badgeClassFor(?string $value): string
    {
        if (empty($value)) {
            return 'bg-light text-muted border';
        }

        $case = self::tryFrom($value);

        return $case ? $case->badgeClass() : 'bg-light text-muted border';
    }

    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
