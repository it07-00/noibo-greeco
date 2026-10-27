<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\DailyReport;
use App\Models\User;

final class DailyReportPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ReportView->value);
    }

    public function view(User $user, DailyReport $report): bool
    {
        // Owner can always view their own
        if ((int) $report->user_id === (int) $user->id) {
            return true;
        }

        return $this->viewAll($user);
    }

    public function viewAll(User $user): bool
    {
        return $user->can(PermissionEnum::ReportView->value)
            && $user->can(PermissionEnum::ReportViewAll->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::ReportCreate->value);
    }

    public function update(User $user, DailyReport $report): bool
    {
        return (int) $report->user_id === (int) $user->id;
    }

    public function delete(User $user, DailyReport $report): bool
    {
        return (int) $report->user_id === (int) $user->id || $user->can(PermissionEnum::ReportDelete->value);
    }
}
