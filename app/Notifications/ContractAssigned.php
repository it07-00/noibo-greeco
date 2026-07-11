<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class ContractAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public int $contractId,
        public string $contractLabel,
        public string $assignerName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-person-check-fill',
            'color' => 'success',
            'contract_id' => $this->contractId,
            'contract_label' => $this->contractLabel,
            'message' => "{$this->assignerName} đã phân công cho bạn thực hiện hợp đồng: {$this->contractLabel}",
            'url' => route('contracts.show', $this->contractId),
        ];
    }
}
