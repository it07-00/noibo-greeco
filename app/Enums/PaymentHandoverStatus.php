<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum PaymentHandoverStatus: string
{
    use HasLocalizedOptions;

    case BusinessPreparing = 'business_preparing';
    case SubmittedToAccounting = 'submitted_to_accounting';
    case AccountingReviewing = 'accounting_reviewing';
    case ReturnedToBusiness = 'returned_to_business';
    case WaitingForCustomerPayment = 'waiting_for_customer_payment';
    case Completed = 'completed';

    public static function translationKey(): string
    {
        return 'payment_handover_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BusinessPreparing => 'bg-secondary-subtle text-secondary',
            self::SubmittedToAccounting => 'bg-primary-subtle text-primary',
            self::AccountingReviewing => 'bg-info-subtle text-info',
            self::ReturnedToBusiness => 'bg-danger-subtle text-danger',
            self::WaitingForCustomerPayment => 'bg-warning-subtle text-warning',
            self::Completed => 'bg-success-subtle text-success',
        };
    }
}
