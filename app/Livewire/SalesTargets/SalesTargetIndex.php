<?php

declare(strict_types=1);

namespace App\Livewire\SalesTargets;

use App\Enums\ContractStatus;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Contract;
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
#[Title('Đăng ký doanh số')]
final class SalesTargetIndex extends Component
{
    public int $year;

    public string $month = '';

    public int|string $targetUserId = 0;

    /**
     * @var array<int, string>
     */
    public array $targetAmounts = [];

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::SalesTargetManage->value);

        $this->year = (int) now()->year;
        $this->targetUserId = $this->isRestrictedSales()
            ? $this->actor()->id
            : $this->defaultTargetUserId();

        $this->loadTargetAmounts();
    }

    public function updatedYear(): void
    {
        $this->loadTargetAmounts();
    }

    public function updatedTargetUserId(): void
    {
        $this->loadTargetAmounts();
    }

    public function saveAnnualTargets(): void
    {
        Gate::authorize(PermissionEnum::SalesTargetManage->value);

        if ($this->isRestrictedSales()) {
            $this->targetUserId = $this->actor()->id;
        }

        $validated = $this->validate([
            'targetUserId' => ['required', 'integer', 'exists:users,id'],
            'targetAmounts' => ['required', 'array', 'size:12'],
            'targetAmounts.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        for ($month = 1; $month <= 12; $month++) {
            $target = SalesTarget::query()->firstOrNew([
                'year' => $this->year,
                'month' => $month,
                'user_id' => $validated['targetUserId'],
            ]);

            $target->target_amount = (int) ($validated['targetAmounts'][$month] ?: 0);
            $target->set_by = $this->actor()->id;
            $target->save();
        }

        $this->loadTargetAmounts();

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã lưu cam kết năm',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }

    public function render(BusinessReportService $reports): View
    {
        $targetCommitments = $this->targetCommitments(
            $reports->monthly($this->year, (int) $this->targetUserId),
        );

        $selectedMonthRow = null;
        $monthContracts = [];

        if ($this->month !== '') {
            $monthInt = (int) $this->month;
            $selectedMonthRow = collect($targetCommitments['rows'])
                ->firstWhere('month', $monthInt);
            $monthContracts = $this->getMonthContracts($monthInt);
        }

        return view('livewire.sales-targets.sales-target-index', [
            'targetCommitments' => $targetCommitments,
            'salesUsers' => User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']),
            'canChooseOwner' => ! $this->isRestrictedSales(),
            'selectedMonthRow' => $selectedMonthRow,
            'monthContracts' => $monthContracts,
        ]);
    }

    private function loadTargetAmounts(): void
    {
        if ($this->isRestrictedSales()) {
            $this->targetUserId = $this->actor()->id;
        }

        $this->targetAmounts = $this->targetAmountsForYear();
        $this->resetValidation();
    }

    /**
     * @return array<int, string>
     */
    private function targetAmountsForYear(): array
    {
        $amounts = array_fill(1, 12, '');

        SalesTarget::query()
            ->where('year', $this->year)
            ->where('user_id', $this->targetUserId)
            ->get(['month', 'target_amount'])
            ->each(static function (SalesTarget $target) use (&$amounts): void {
                $amounts[$target->month] = (string) $target->target_amount;
            });

        return $amounts;
    }

    private function defaultTargetUserId(): int
    {
        return (int) (User::role(RoleEnum::Sales->value)->orderBy('name')->value('id') ?: $this->actor()->id);
    }

    /**
     * @param  list<array{month: int, target: int, target_contracts: int, signed: int, collected: int, contracts: int, percent: float}>  $monthly
     * @return array{rows: list<array{month: int, quarter: int, is_current: bool, target: int, signed: int, difference: int, percent: float, status: string, status_label: string}>, total_target: int, total_signed: int, total_difference: int, total_percent: float}
     */
    private function targetCommitments(array $monthly): array
    {
        $rows = [];
        $totalTarget = 0;
        $totalSigned = 0;

        foreach ($monthly as $row) {
            $target = (int) ($this->targetAmounts[$row['month']] ?: 0);
            $signed = (int) $row['signed'];
            $difference = $signed - $target;
            $percent = $target > 0 ? round($signed / $target * 100, 1) : 0.0;
            $status = $percent >= 100 ? 'met' : ($percent >= 75 ? 'near' : 'missed');

            $rows[] = [
                'month' => $row['month'],
                'quarter' => (int) ceil($row['month'] / 3),
                'is_current' => $this->year === (int) now()->year && $row['month'] === (int) now()->month,
                'target' => $target,
                'signed' => $signed,
                'difference' => $difference,
                'percent' => $percent,
                'status' => $status,
                'status_label' => match ($status) {
                    'met' => 'Đạt',
                    'near' => 'Gần đạt',
                    default => 'Chưa đạt',
                },
            ];

            $totalTarget += $target;
            $totalSigned += $signed;
        }

        return [
            'rows' => $rows,
            'total_target' => $totalTarget,
            'total_signed' => $totalSigned,
            'total_difference' => $totalSigned - $totalTarget,
            'total_percent' => $totalTarget > 0 ? round($totalSigned / $totalTarget * 100, 1) : 0.0,
        ];
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
     * @return array<int, array{customer: string, service: string, value: int, payment_method: string, notes: string}>
     */
    private function getMonthContracts(int $month): array
    {
        return Contract::query()
            ->with(['customer', 'services'])
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $this->year)
            ->whereMonth('signed_at', $month)
            ->where('owner_id', (int) $this->targetUserId)
            ->orderByDesc('value')
            ->get()
            ->map(fn (Contract $contract): array => [
                'customer' => $contract->customer?->name ?? '—',
                'service'  => $contract->services
                    ->map(fn ($s) => $s->service_type->label())
                    ->unique()
                    ->join(', ') ?: '—',
                'value'          => $contract->value,
                'payment_method' => $contract->payment_method?->label() ?? '—',
                'notes'          => $contract->notes ?? '',
            ])
            ->toArray();
    }
}
