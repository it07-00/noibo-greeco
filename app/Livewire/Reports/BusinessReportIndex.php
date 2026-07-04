<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\SalesTarget;
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

    public int $targetUserId = 0;

    public int $targetMonth = 1;

    public string $targetAmount = '';

    public string $targetContractCount = '';

    public string $targetNotes = '';

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::SalesReportView->value);
        $this->year = (int) now()->year;
        $this->targetMonth = (int) now()->month;

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

    public function openTarget(): void
    {
        Gate::authorize(PermissionEnum::SalesTargetManage->value);
        $user = $this->actor();
        $this->targetUserId = $this->isRestrictedSales()
            ? $user->id
            : (int) ($this->ownerId !== '' ? $this->ownerId : $user->id);
        $this->targetMonth = $this->month !== '' ? (int) $this->month : (int) now()->month;
        $target = SalesTarget::query()
            ->where('year', $this->year)
            ->where('month', $this->targetMonth)
            ->where('user_id', $this->targetUserId)
            ->first();
        $this->targetAmount = $target ? (string) $target->target_amount : '';
        $this->targetContractCount = $target ? (string) $target->target_contract_count : '';
        $this->targetNotes = $target?->notes ?? '';
        $this->resetValidation();
        $this->dispatch('sales-target:show');
    }

    public function saveTarget(): void
    {
        Gate::authorize(PermissionEnum::SalesTargetManage->value);

        if ($this->isRestrictedSales()) {
            $this->targetUserId = $this->actor()->id;
        }

        $validated = $this->validate([
            'targetUserId' => ['required', 'integer', 'exists:users,id'],
            'targetMonth' => ['required', 'integer', 'between:1,12'],
            'targetAmount' => ['required', 'integer', 'min:0'],
            'targetContractCount' => ['required', 'integer', 'min:0'],
            'targetNotes' => ['nullable', 'string', 'max:1000'],
        ]);

        SalesTarget::query()->updateOrCreate(
            [
                'year' => $this->year,
                'month' => $validated['targetMonth'],
                'user_id' => $validated['targetUserId'],
            ],
            [
                'target_amount' => (int) $validated['targetAmount'],
                'target_contract_count' => (int) $validated['targetContractCount'],
                'notes' => $validated['targetNotes'] ?: null,
                'set_by' => $this->actor()->id,
            ],
        );

        $this->dispatch('sales-target:hide');
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã lưu KPI kinh doanh',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }

    public function render(BusinessReportService $reports): View
    {
        $ownerId = $this->resolvedOwnerId();
        $month = $this->month !== '' ? (int) $this->month : null;

        return view('livewire.reports.business-report-index', [
            'summary' => $reports->summary($this->year, $month, $ownerId),
            'monthly' => $reports->monthly($this->year, $ownerId),
            'ranking' => $reports->ranking($this->year, $month),
            'contractTypes' => $reports->contractTypes($this->year, $month, $ownerId),
            'contractServicesStructure' => $reports->contractServicesStructure($this->year, $month, $ownerId),
            'salesBySource' => $reports->salesBySource($this->year, $month, $ownerId),
            'serviceConversionRates' => $reports->serviceConversionRates($this->year, $month, $ownerId),
            'regionalBreakdown' => $reports->regionalBreakdown($this->year, $month, $ownerId),
            'overdueSchedules' => $reports->overdueSchedules($ownerId),
            'salesUsers' => User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']),
            'canSetTarget' => $this->actor()->can(PermissionEnum::SalesTargetManage->value),
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
        return $this->actor()->hasRole(RoleEnum::Sales->value)
            && ! $this->actor()->can(PermissionEnum::ManagementDashboardView->value);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
