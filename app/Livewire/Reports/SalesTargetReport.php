<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\PermissionEnum;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\Quotation;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Báo cáo doanh số cam kết')]
final class SalesTargetReport extends Component
{
    public int $year;

    public string $ownerId = '';

    public int $viewMonth;

    public string $viewMode = 'year';

    public array $detail = [];

    public array $potentialDetail = [];

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::SalesReportView->value);

        $this->year = (int) now()->year;
        $this->viewMonth = (int) now()->month;

        if ($this->isRestrictedSales()) {
            $this->ownerId = (string) Auth::id();
        }

        $this->loadMonthDetail();
    }

    public function switchMode(string $mode): void
    {
        if (in_array($mode, ['year', 'month'], true)) {
            $this->viewMode = $mode;
        }
    }

    public function updatedYear(): void
    {
        $this->loadMonthDetail();
    }

    public function updatedOwnerId(): void
    {
        if ($this->isRestrictedSales()) {
            $this->ownerId = (string) Auth::id();
        }
        $this->loadMonthDetail();
    }

    public function updatedViewMonth(): void
    {
        $this->loadMonthDetail();
    }

    public function openDetail(int $month): void
    {
        $this->viewMonth = $month;
        $this->viewMode = 'month';
        $this->loadMonthDetail();
    }

    private function loadMonthDetail(): void
    {
        $ownerId = $this->resolvedOwnerId();

        // 1. Fetch Signed Contracts Detail
        $contracts = Contract::query()
            ->with(['customer', 'services', 'owner'])
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $this->year)
            ->whereMonth('signed_at', $this->viewMonth)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get();

        $this->detail = $contracts->map(fn (Contract $contract): array => [
            'customer' => $contract->customer?->name ?? '—',
            'staff' => $contract->owner?->name ?? '—',
            'type' => $contract->services
                ->map(fn ($s) => $s->service_type->label())
                ->unique()
                ->join(', ') ?: '—',
            'value' => (float) $contract->value,
            'is_renewal' => $contract->renewal_status === ContractRenewalStatus::Renewed,
            'date' => $contract->signed_at ? $contract->signed_at->format('d/m/Y') : '—',
            'notes' => $contract->notes ?? '',
        ])->toArray();

        // 2. Fetch Potential Quotations Detail
        $quotations = Quotation::query()
            ->with(['customer', 'services', 'owner'])
            ->whereIn('status', [QuotationStatus::Sent->value, QuotationStatus::FollowingUp->value])
            ->whereYear('issued_at', $this->year)
            ->whereMonth('issued_at', $this->viewMonth)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get();

        $this->potentialDetail = $quotations->map(fn (Quotation $quotation): array => [
            'company' => $quotation->customer?->name ?? '—',
            'service' => $quotation->services
                ->map(fn ($s) => $s->service_type->label())
                ->unique()
                ->join(', ') ?: '—',
            'staff' => $quotation->owner?->name ?? '—',
            'value' => (float) $quotation->contract_value,
            'date' => $quotation->issued_at ? $quotation->issued_at->format('d/m/Y') : '—',
            'notes' => $quotation->notes ?? '',
        ])->toArray();
    }

    public function totalPct(array $totals): ?float
    {
        $target = (float) ($totals['target'] ?? 0);
        $actual = (float) ($totals['actual'] ?? 0);

        return $target > 0 ? round(($actual / $target) * 100, 1) : null;
    }

    public function totalDelta(array $totals): float
    {
        return ((float) ($totals['actual'] ?? 0) + (float) ($totals['potential'] ?? 0)) - (float) ($totals['target'] ?? 0);
    }

    public function monthMetrics(array $data): array
    {
        $target = (float) ($data['target'] ?? 0);
        $actual = (float) ($data['actual'] ?? 0);
        $potential = (float) ($data['potential'] ?? 0);
        $pct = $target > 0 ? round($actual / $target * 100, 1) : null;

        return [
            'target' => $target,
            'actual' => $actual,
            'potential' => $potential,
            'pct' => $pct,
            'delta' => ($actual + $potential) - $target,
            'progressWidth' => $pct !== null ? max(0, min(100, $pct)) : 0,
            'progressClass' => $pct === null
                ? 'bg-secondary'
                : ($pct >= 100 ? 'bg-success' : ($pct >= 70 ? 'bg-warning' : 'bg-danger')),
        ];
    }

    public function pctTextClass(?float $pct): string
    {
        if ($pct === null) return 'text-danger';
        if ($pct >= 100) return 'text-success';
        if ($pct >= 70) return 'text-warning';
        return 'text-danger';
    }

    public function pctBadgeClass(?float $pct): string
    {
        if ($pct === null) return 'bg-danger-subtle text-danger';
        if ($pct >= 100) return 'bg-success-subtle text-success';
        if ($pct >= 70) return 'bg-warning-subtle text-warning';
        return 'bg-danger-subtle text-danger';
    }

    public function render(): View
    {
        $ownerId = $this->resolvedOwnerId();
        $maxMonth = $this->year >= (int) now()->year ? (int) now()->month : 12;

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = ['target' => 0.0, 'actual' => 0.0, 'potential' => 0.0];
        }

        // Fetch Targets
        $targets = SalesTarget::query()
            ->where('year', $this->year)
            ->when($ownerId !== null, static fn ($query) => $query->where('user_id', $ownerId))
            ->get();
        foreach ($targets as $t) {
            $mIdx = (int) $t->month;
            if (isset($months[$mIdx])) {
                $months[$mIdx]['target'] += (float) $t->target_amount;
            }
        }

        // Fetch Actuals
        $contracts = Contract::query()
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $this->year)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get();
        foreach ($contracts as $c) {
            $mIdx = (int) $c->signed_at->month;
            if (isset($months[$mIdx])) {
                $months[$mIdx]['actual'] += (float) $c->value;
            }
        }

        // Fetch Potentials (sent/following_up quotes)
        $quotations = Quotation::query()
            ->whereIn('status', [QuotationStatus::Sent->value, QuotationStatus::FollowingUp->value])
            ->whereYear('issued_at', $this->year)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get();
        foreach ($quotations as $q) {
            $mIdx = (int) $q->issued_at->month;
            if (isset($months[$mIdx])) {
                $months[$mIdx]['potential'] += (float) $q->contract_value;
            }
        }

        // Calculate Totals
        $totals = ['target' => 0.0, 'actual' => 0.0, 'potential' => 0.0];
        foreach ($months as $m) {
            $totals['target'] += $m['target'];
            $totals['actual'] += $m['actual'];
            $totals['potential'] += $m['potential'];
        }

        return view('livewire.reports.sales-target-report', [
            'months' => $months,
            'totals' => $totals,
            'monthTarget' => (float) ($months[$this->viewMonth]['target'] ?? 0),
            'monthActual' => (float) ($months[$this->viewMonth]['actual'] ?? 0),
            'monthPotential' => (float) ($months[$this->viewMonth]['potential'] ?? 0),
            'monthRemain' => max(0.0, (float) ($months[$this->viewMonth]['target'] ?? 0) - ((float) ($months[$this->viewMonth]['actual'] ?? 0) + (float) ($months[$this->viewMonth]['potential'] ?? 0))),
            'monthPct' => isset($months[$this->viewMonth]) ? $this->monthMetrics($months[$this->viewMonth])['pct'] : null,
            'salesUsers' => User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']),
            'canChooseOwner' => ! $this->isRestrictedSales(),
            'maxMonth' => $maxMonth,
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
        return ! $this->actor()->can(PermissionEnum::ManagementDashboardView->value);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
