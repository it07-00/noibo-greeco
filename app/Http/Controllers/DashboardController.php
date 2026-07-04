<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\DashboardService;
use App\Services\Reports\BusinessReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

final class DashboardController extends Controller
{
    public function __invoke(
        DashboardService $dashboard,
        BusinessReportService $businessReports,
    ): View {
        $user = Auth::user();
        $canViewCommerce = $user instanceof User
            && $user->can(PermissionEnum::SalesReportView->value);
        $ownerId = $user instanceof User
            && $user->hasRole(RoleEnum::Sales->value)
            && ! $user->can(PermissionEnum::ManagementDashboardView->value)
                ? $user->id
                : null;

        return view('dashboard', [
            'stats' => $dashboard->stats(),
            'reportStatus' => $dashboard->reportStatus(),
            'roleDistribution' => $dashboard->roleDistribution(),
            'recentSchedules' => $dashboard->recentSchedules(),
            'recentReports' => $dashboard->recentReports(),
            'commerce' => $canViewCommerce ? $businessReports->homeSnapshot($ownerId) : null,
        ]);
    }
}
