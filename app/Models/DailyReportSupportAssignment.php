<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SupportRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DailyReportSupportAssignment extends Model
{
    protected $fillable = [
        'daily_report_id',
        'assignee_id',
        'status',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SupportRequestStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
