<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DailyReportSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private readonly DailyReport $report,
        private readonly string $reporterName,
        private readonly int $lateDays = 0,
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
        return [
            'title' => 'Báo cáo ngày mới',
            'message' => $this->reporterName.' đã gửi báo cáo ngày '.$this->report->report_date->format('d/m/Y')
                .($this->lateDays > 0 ? " (nộp trễ {$this->lateDays} ngày)" : ''),
            'report_id' => $this->report->id,
            'reporter_name' => $this->reporterName,
            'report_date' => $this->report->report_date->toDateString(),
            'late_days' => $this->lateDays,
            'icon' => 'fi-rr-document',
            'url' => '/daily-reports',
        ];
    }
}
