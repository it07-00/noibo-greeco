<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum ContractType: string
{
    use HasLocalizedOptions;

    case Training = 'training';
    case Consulting = 'consulting';
    case Project = 'project';
    case ResearchTechnologyTransfer = 'research_technology_transfer';
    case ScientificEventCommunication = 'scientific_event_communication';

    public static function translationKey(): string
    {
        return 'contract_type';
    }
}
