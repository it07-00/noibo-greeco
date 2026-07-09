<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\User;
use App\Services\Reports\BusinessReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Báo cáo kinh doanh')]
final class BusinessReportIndex extends Component
{
    public int $year;

    public string $month = '';

    public string $ownerId = '';

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::SalesReportView->value);

        $actor = $this->actor();
        abort_unless(
            $actor->hasRole(RoleEnum::Sales->value)
            || $actor->hasRole(RoleEnum::Director->value)
            || $actor->hasRole(RoleEnum::SuperAdmin->value),
            403,
            'Chỉ có Giám đốc hoặc nhân viên Kinh doanh mới có quyền truy cập báo cáo này.'
        );

        $this->year = (int) now()->year;

        if ($this->isRestrictedSales()) {
            $this->ownerId = (string) Auth::id();
        }
    }

    public function updatedOwnerId(): void
    {
        if ($this->isRestrictedSales()) {
            $this->ownerId = (string) Auth::id();
        }
    }

    public function render(BusinessReportService $reports): View
    {
        $ownerId = $this->resolvedOwnerId();
        $month = $this->month !== '' ? (int) $this->month : null;
        $monthly = $reports->monthly($this->year, $ownerId);

        return view('livewire.reports.business-report-index', [
            'summary' => $reports->summary($this->year, $month, $ownerId),
            'monthly' => $monthly,
            'ranking' => $reports->ranking($this->year, $month),
            'contractTypes' => $reports->contractTypes($this->year, $month, $ownerId),
            'contractServicesStructure' => $reports->contractServicesStructure($this->year, $month, $ownerId),
            'salesBySource' => $reports->salesBySource($this->year, $month, $ownerId),
            'serviceConversionRates' => $reports->serviceConversionRates($this->year, $month, $ownerId),
            'regionalBreakdown' => $reports->regionalBreakdown($this->year, $month, $ownerId),
            'overdueSchedules' => $reports->overdueSchedules($ownerId),
            'salesUsers' => User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']),
            'canChooseOwner' => ! $this->isRestrictedSales(),
        ]);
    }

    private function resolvedOwnerId(): ?int
    {
        if ($this->isRestrictedSales()) {
            return $this->actor()->id;
        }

        return $this->ownerId !== '' ? (int) $this->ownerId : null;
    }

    private function isRestrictedSales(): bool
    {
        return ! $this->actor()->can(PermissionEnum::ManagementDashboardView->value)
            && ! $this->actor()->can(PermissionEnum::SalesReportViewAll->value);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
