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
        \Illuminate\Http\Request $request,
        DashboardService $dashboard,
        BusinessReportService $businessReports,
    ): View {
        $user = Auth::user();
        $canViewCommerce = $user instanceof User
            && $user->can(PermissionEnum::SalesReportView->value);

        $ownerId = null;
        $isRestrictedSales = false;

        if ($user instanceof User) {
            $isRestrictedSales = $user->hasRole(RoleEnum::Sales->value)
                && !$user->can(PermissionEnum::ManagementDashboardView->value);
            if ($isRestrictedSales) {
                $ownerId = $user->id;
            }
        }

        $canChooseOwner = !$isRestrictedSales && $canViewCommerce;
        if ($canChooseOwner && $request->filled('owner_id')) {
            $ownerId = $request->integer('owner_id');
        }

        $year = $request->integer('year', (int) now()->year);
        $month = $request->has('month') && $request->input('month') !== ''
            ? ($request->input('month') === 'all' ? null : $request->integer('month'))
            : (int) now()->month;

        return view('dashboard', [
            'stats' => $dashboard->stats(),
            'reportStatus' => $dashboard->reportStatus(),
            'roleDistribution' => $dashboard->roleDistribution(),
            'recentSchedules' => $dashboard->recentSchedules(),
            'recentReports' => $dashboard->recentReports(),
            'commerce' => $canViewCommerce ? $businessReports->summary($year, $month, $ownerId) : null,
            'contractServicesStructure' => $canViewCommerce ? $businessReports->contractServicesStructure($year, $month, $ownerId) : null,
            'salesBySource' => $canViewCommerce ? $businessReports->salesBySource($year, $month, $ownerId) : null,
            'serviceConversionRates' => $canViewCommerce ? $businessReports->serviceConversionRates($year, $month, $ownerId) : null,
            'regionalBreakdown' => $canViewCommerce ? $businessReports->regionalBreakdown($year, $month, $ownerId) : null,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'selectedOwnerId' => $ownerId,
            'canChooseOwner' => $canChooseOwner,
            'salesUsers' => $canChooseOwner ? User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }
}
