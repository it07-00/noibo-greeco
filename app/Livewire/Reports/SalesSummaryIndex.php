<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Bảng tổng kết doanh số')]
final class SalesSummaryIndex extends Component
{
    public int $year;

    public string $ownerId = '';

    public string $month = '0';

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::SalesReportView->value);

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

    public function updatedYear(): void
    {
        $maxMonth = $this->year >= (int) now()->year ? (int) now()->month : 12;
        if ($this->month !== '0' && (int) $this->month > $maxMonth) {
            $this->month = '0';
        }
    }

    public function render(): View
    {
        $ownerId = $this->resolvedOwnerId();
        $maxMonth = $this->year >= (int) now()->year ? (int) now()->month : 12;
        
        $salesSummary = $this->getSalesSummaryTable($ownerId, $maxMonth);
        
        $monthContracts = collect();
        if ($this->month !== '0') {
            $monthContracts = $this->getMonthContracts((int) $this->month, $ownerId);
        }

        return view('livewire.reports.sales-summary-index', [
            'salesSummary' => $salesSummary['months'],
            'totals' => $salesSummary['totals'],
            'detail' => $monthContracts,
            'maxMonth' => $maxMonth,
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
        return ! $this->actor()->can(PermissionEnum::ManagementDashboardView->value);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    /**
     * @return array{
     *     months: array<int, array{renewal: float, progressive: float, contract_total: float, renewal_count: int, progressive_count: int}>,
     *     totals: array{renewal: float, progressive: float, contract_total: float, renewal_count: int, progressive_count: int, grand: float}
     * }
     */
    private function getSalesSummaryTable(?int $ownerId, int $maxMonth): array
    {
        $contracts = Contract::query()
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $this->year)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get();

        $months = [];
        for ($m = 1; $m <= $maxMonth; $m++) {
            $months[$m] = [
                'renewal' => 0.0,
                'progressive' => 0.0,
                'contract_total' => 0.0,
                'renewal_count' => 0,
                'progressive_count' => 0,
            ];
        }

        foreach ($contracts as $contract) {
            $month = (int) $contract->signed_at->month;
            if ($month > $maxMonth) {
                continue;
            }
            $value = (float) $contract->value;

            $isRenewal = $contract->renewal_status === ContractRenewalStatus::Renewed;

            if ($isRenewal) {
                $months[$month]['renewal'] += $value;
                $months[$month]['renewal_count'] += 1;
            } else {
                $months[$month]['progressive'] += $value;
                $months[$month]['progressive_count'] += 1;
            }
        }

        for ($m = 1; $m <= $maxMonth; $m++) {
            $months[$m]['contract_total'] = $months[$m]['renewal'] + $months[$m]['progressive'];
        }

        $totals = [
            'renewal' => array_sum(array_column($months, 'renewal')),
            'progressive' => array_sum(array_column($months, 'progressive')),
            'contract_total' => array_sum(array_column($months, 'contract_total')),
            'renewal_count' => array_sum(array_column($months, 'renewal_count')),
            'progressive_count' => array_sum(array_column($months, 'progressive_count')),
        ];
        $totals['grand'] = $totals['contract_total'];

        return [
            'months' => $months,
            'totals' => $totals,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{customer: string, type: string, value: float, is_renewal: bool, date: ?string}>
     */
    private function getMonthContracts(int $month, ?int $ownerId)
    {
        return Contract::query()
            ->with(['customer', 'services'])
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $this->year)
            ->whereMonth('signed_at', $month)
            ->when($ownerId !== null, static fn ($query) => $query->where('owner_id', $ownerId))
            ->get()
            ->map(fn (Contract $contract): array => [
                'customer' => $contract->customer?->name ?? '—',
                'type' => $contract->services
                    ->map(fn ($s) => $s->service_type->label())
                    ->unique()
                    ->join(', ') ?: '—',
                'value' => (float) $contract->value,
                'is_renewal' => $contract->renewal_status === ContractRenewalStatus::Renewed,
                'date' => $contract->signed_at ? $contract->signed_at->format('Y-m-d') : null,
            ])
            ->sortByDesc('date')
            ->values();
    }
}
