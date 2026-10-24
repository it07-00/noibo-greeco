<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

final class ContractProgressNoted extends Notification
{
    public function __construct(public int $contractId, public string $contractLabel, public string $userName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['icon' => 'bi-chat-left-text', 'color' => 'info', 'contract_id' => $this->contractId,
            'message' => $this->userName.' đã thêm ghi chú cho hợp đồng '.$this->contractLabel,
            'url' => route('contracts.show', $this->contractId)];
    }
}
