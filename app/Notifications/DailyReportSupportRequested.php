<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DailyReportSupportRequested extends Notification
{
    use Queueable;

    public function __construct(
        private readonly DailyReport $report,
        private readonly string $reporterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Yêu cầu hỗ trợ mới',
            'message' => $this->reporterName.' cần bạn hỗ trợ: '.str($this->report->issues)->limit(100),
            'report_id' => $this->report->id,
            'icon' => 'fi-rr-life-ring',
            'url' => '/daily-reports?view=support',
        ];
    }
}
