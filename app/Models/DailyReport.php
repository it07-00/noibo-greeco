<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class DailyReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const LATE_SUBMISSION_THRESHOLD_DAYS = 3;

    protected $fillable = [
        'user_id',
        'report_date',
        'work_done',
        'plan_tomorrow',
        'issues',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supportAssignments(): HasMany
    {
        return $this->hasMany(DailyReportSupportAssignment::class);
    }

    public function lateDays(): int
    {
        if ($this->created_at === null || $this->report_date === null) {
            return 0;
        }

        $days = (int) $this->report_date
            ->copy()
            ->startOfDay()
            ->diffInDays($this->created_at->copy()->startOfDay(), false);

        return $days >= self::LATE_SUBMISSION_THRESHOLD_DAYS ? $days : 0;
    }
}
