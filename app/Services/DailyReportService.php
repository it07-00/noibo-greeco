<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\DailyReportDTO;
use App\Enums\RoleEnum;
use App\Models\DailyReport;
use App\Models\DailyReportSupportAssignment;
use App\Models\User;
use App\Notifications\DailyReportSubmitted;
use App\Notifications\DailyReportSupportRequested;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class DailyReportService
{
    /**
     * Get paginated reports, optionally filtered.
     *
     * @param  int|null  $userId  Filter by specific user (null = all)
     * @param  string|null  $date  Filter by specific date (Y-m-d)
     * @param  string|null  $search  Search in work_done
     * @return LengthAwarePaginator<DailyReport>
     */
    public function getReports(
        ?int $userId = null,
        ?string $date = null,
        ?string $search = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        return DailyReport::query()
            ->with('user')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($date, fn ($q) => $q->whereDate('report_date', $date))
            ->when($search, fn ($q) => $q->where('work_done', 'like', "%{$search}%"))
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get reports in a date range for calendar view.
     *
     * @return Collection<int, DailyReport>
     */
    public function getReportsInRange(string $start, string $end, ?int $userId = null): Collection
    {
        return DailyReport::query()
            ->with('user')
            ->whereBetween('report_date', [$start, $end])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->orderBy('report_date')
            ->get();
    }

    public function createReport(DailyReportDTO $dto, array $supportUserIds = []): DailyReport
    {
        $report = DB::transaction(function () use ($dto): DailyReport {
            return DailyReport::create([
                'user_id' => $dto->userId,
                'report_date' => $dto->reportDate,
                'work_done' => $dto->workDone,
                'plan_tomorrow' => $dto->planTomorrow,
                'issues' => $dto->issues,
            ]);
        });

        $this->notifyDirectors($report);
        $this->syncSupportAssignments($report, $supportUserIds);

        return $report;
    }

    public function updateReport(DailyReport $report, DailyReportDTO $dto, array $supportUserIds = []): DailyReport
    {
        $updated = DB::transaction(function () use ($report, $dto): DailyReport {
            $report->update([
                'report_date' => $dto->reportDate,
                'work_done' => $dto->workDone,
                'plan_tomorrow' => $dto->planTomorrow,
                'issues' => $dto->issues,
            ]);

            return $report->refresh();
        });

        $this->syncSupportAssignments($updated, $supportUserIds);

        return $updated;
    }

    public function deleteReport(DailyReport $report): void
    {
        DB::transaction(fn () => $report->delete());
    }

    /**
     * Check if a user already has a report for given date (excluding a record id).
     */
    public function existsForDate(int $userId, string $date, ?int $excludeId = null): bool
    {
        return DailyReport::query()
            ->where('user_id', $userId)
            ->whereDate('report_date', $date)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    /**
     * Notify all users with Director role about a new daily report.
     */
    private function notifyDirectors(DailyReport $report): void
    {
        $report->loadMissing('user');
        $reporterName = $report->user?->name ?? 'N/A';

        $directors = User::role(RoleEnum::Director->value)->get();

        if ($directors->isNotEmpty()) {
            Notification::send($directors, new DailyReportSubmitted($report, $reporterName));
        }
    }

    private function syncSupportAssignments(DailyReport $report, array $supportUserIds): void
    {
        $userIds = collect($supportUserIds)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0 && $id !== $report->user_id)
            ->unique()
            ->values();

        $existingIds = $report->supportAssignments()->pluck('assignee_id');
        $newIds = $userIds->diff($existingIds);

        $report->supportAssignments()->whereNotIn('assignee_id', $userIds)->delete();

        foreach ($newIds as $userId) {
            DailyReportSupportAssignment::query()->create([
                'daily_report_id' => $report->id,
                'assignee_id' => $userId,
            ]);
        }

        if ($newIds->isNotEmpty()) {
            $assignees = User::query()->whereKey($newIds)->get();
            Notification::send(
                $assignees,
                new DailyReportSupportRequested($report, $report->user?->name ?? 'Một đồng nghiệp'),
            );
        }
    }
}
