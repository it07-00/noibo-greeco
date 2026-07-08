<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CommissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class CommissionRequestUpdated extends Notification
{
    use Queueable;

    public function __construct(
        private readonly CommissionRequest $request,
        private readonly string $actorName,
        private readonly string $action, // 'created', 'approved', 'rejected', 'paid'
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $contractNumber = $this->request->contract?->contract_number ?: ('#' . $this->request->contract_id);
        $receiverName = $this->request->receiver_name;
        $amount = number_format($this->request->amount, 0, ',', '.') . '₫';

        $title = 'Yêu cầu chi hoa hồng';
        $message = '';
        $icon = 'fi-rr-receipt';

        switch ($this->action) {
            case 'created':
                $title = 'Yêu cầu hoa hồng mới';
                $message = "{$this->actorName} đã gửi yêu cầu chi hoa hồng {$amount} cho hợp đồng {$contractNumber} (Người nhận: {$receiverName}).";
                $icon = 'fi-rr-file-medical';
                break;
            case 'approved':
                $title = 'Yêu cầu hoa hồng đã duyệt';
                $message = "Yêu cầu chi hoa hồng {$amount} cho hợp đồng {$contractNumber} đã được phê duyệt.";
                $icon = 'fi-rr-checkbox';
                break;
            case 'rejected':
                $title = 'Yêu cầu hoa hồng bị từ chối';
                $reason = $this->rejectionReason($this->request->notes);
                $message = "Yêu cầu chi hoa hồng {$amount} cho hợp đồng {$contractNumber} bị từ chối." . ($reason !== '' ? " Lý do: {$reason}" : '');
                $icon = 'fi-rr-cross-circle';
                break;
            case 'paid':
                $title = 'Yêu cầu hoa hồng đã chi';
                $message = "Yêu cầu chi hoa hồng {$amount} cho hợp đồng {$contractNumber} đã được thanh toán thành công.";
                $icon = 'fi-rr-dollar';
                break;
        }

        return [
            'title' => $title,
            'message' => $message,
            'request_id' => $this->request->id,
            'contract_id' => $this->request->contract_id,
            'contract_number' => $contractNumber,
            'amount' => $this->request->amount,
            'actor_name' => $this->actorName,
            'action' => $this->action,
            'icon' => $icon,
            'url' => '/commissions',
        ];
    }

    private function rejectionReason(?string $notes): string
    {
        return $notes && str_contains($notes, 'Lý do từ chối (kế toán):')
            ? trim(Str::afterLast($notes, 'Lý do từ chối (kế toán):'))
            : '';
    }
}
