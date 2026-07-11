<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class ContractWorkflowUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public int $contractId,
        public string $contractLabel,
        public string $stepName,
        public string $stepLabel,
        public string $userName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-diagram-3-fill',
            'color' => 'info',
            'contract_id' => $this->contractId,
            'contract_label' => $this->contractLabel,
            'message' => "{$this->userName} đã hoàn thành bước \"{$this->stepLabel}\" của hợp đồng {$this->contractLabel}",
            'url' => route('contracts.show', $this->contractId),
        ];
    }
}
