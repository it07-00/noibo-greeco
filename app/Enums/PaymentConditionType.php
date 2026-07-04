<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum PaymentConditionType: string
{
    use HasLocalizedOptions;

    case AfterContractSigned = 'after_contract_signed';
    case AfterMilestoneCompleted = 'after_milestone_completed';
    case AfterAcceptance = 'after_acceptance';
    case AfterInvoiceIssued = 'after_invoice_issued';
    case FixedDate = 'fixed_date';
    case Custom = 'custom';

    public static function translationKey(): string
    {
        return 'payment_condition_type';
    }
}
