<?php

declare(strict_types=1);

namespace App\Enums;

enum ContractVoucherStatus: string
{
    case PaymentRequested = 'payment_requested';
    case Invoiced = 'invoiced';
    case HandedOver = 'handed_over';
    case Accepted = 'accepted';

    public function label(): string
    {
        return match ($this) {
            self::PaymentRequested => 'Đã đề nghị thanh toán/tạm ứng',
            self::Invoiced => 'Đã xuất hóa đơn',
            self::HandedOver => 'Đã làm biên bản bàn giao hồ sơ',
            self::Accepted => 'Đã làm BB bàn giao và nghiệm thu kết thúc hợp đồng',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
