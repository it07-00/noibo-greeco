<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum DocumentType: string
{
    use HasLocalizedOptions;

    case Contract = 'contract';
    case PaymentRequest = 'payment_request';
    case Invoice = 'invoice';
    case AcceptanceMinutes = 'acceptance_minutes';
    case HandoverMinutes = 'handover_minutes';
    case LiquidationMinutes = 'liquidation_minutes';
    case PaymentProof = 'payment_proof';
    case Other = 'other';

    public static function translationKey(): string
    {
        return 'document_type';
    }
}
