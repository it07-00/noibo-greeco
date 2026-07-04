<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Enums\ContractStatus;
use App\Enums\PaymentScheduleStatus;
use App\Enums\QuotationStatus;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\Quotation;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class BusinessReportService
{
    /**
     * @return array<string, int|float>
     */
    public function summary(int $year, ?int $month = null, ?int $ownerId = null): array
    {
        $periodContracts = $this->contractQuery($year, $month, $ownerId);
        $signedValue = (int) (clone $periodContracts)->sum('value');
        $contractCount = (clone $periodContracts)->count();

        $collectionQuery = ContractPayment::query()
            ->whereNull('voided_at')
            ->whereYear('paid_at', $year)
            ->when($month !== null, static fn (Builder $query) => $query->whereMonth('paid_at', $month))
            ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                'contract',
                static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
            ));

        $targetQuery = SalesTarget::query()
            ->where('year', $year)
            ->when($month !== null, static fn (Builder $query) => $query->where('month', $month))
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('user_id', $ownerId));
        $target = (int) $targetQuery->sum('target_amount');
        $targetContractCount = (int) (clone $targetQuery)->sum('target_contract_count');

        $currentContracts = Contract::query()
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId));
        $currentContractValue = (int) (clone $currentContracts)->sum('value');
        $receivedToDate = (int) ContractPayment::query()
            ->whereNull('voided_at')
            ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                'contract',
                static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
            ))
            ->sum('amount');

        $pipeline = (int) Quotation::query()
            ->whereIn('status', [QuotationStatus::Sent->value, QuotationStatus::FollowingUp->value])
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId))
            ->sum('contract_value');

        $closedQuotations = Quotation::query()
            ->whereYear('issued_at', $year)
            ->when($month !== null, static fn (Builder $query) => $query->whereMonth('issued_at', $month))
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId))
            ->whereIn('status', [QuotationStatus::Won->value, QuotationStatus::Lost->value]);
        $closedCount = (clone $closedQuotations)->count();
        $wonCount = (clone $closedQuotations)->where('status', QuotationStatus::Won->value)->count();

        return [
            'signed_value' => $signedValue,
            'contract_count' => $contractCount,
            'collected' => (int) $collectionQuery->sum('amount'),
            'target' => $target,
            'target_percent' => $target > 0 ? round($signedValue / $target * 100, 1) : 0,
            'target_contract_count' => $targetContractCount,
            'contract_count_percent' => $targetContractCount > 0
                ? round($contractCount / $targetContractCount * 100, 1)
                : 0,
            'outstanding' => max(0, $currentContractValue - $receivedToDate),
            'pipeline' => $pipeline,
            'conversion_rate' => $closedCount > 0 ? round($wonCount / $closedCount * 100, 1) : 0,
        ];
    }

    /**
     * @return list<array{month: int, target: int, target_contracts: int, signed: int, collected: int, contracts: int, percent: float}>
     */
    public function monthly(int $year, ?int $ownerId = null): array
    {
        $rows = [];

        for ($month = 1; $month <= 12; $month++) {
            $contracts = $this->contractQuery($year, $month, $ownerId);
            $signed = (int) (clone $contracts)->sum('value');
            $target = (int) SalesTarget::query()
                ->where('year', $year)
                ->where('month', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->where('user_id', $ownerId))
                ->sum('target_amount');
            $targetContracts = (int) SalesTarget::query()
                ->where('year', $year)
                ->where('month', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->where('user_id', $ownerId))
                ->sum('target_contract_count');
            $collected = (int) ContractPayment::query()
                ->whereNull('voided_at')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                    'contract',
                    static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
                ))
                ->sum('amount');

            $rows[] = [
                'month' => $month,
                'target' => $target,
                'target_contracts' => $targetContracts,
                'signed' => $signed,
                'collected' => $collected,
                'contracts' => (clone $contracts)->count(),
                'percent' => $target > 0 ? round($signed / $target * 100, 1) : 0,
            ];
        }

        return $rows;
    }

    /**
     * @return Collection<int, array<string, int|float|string>>
     */
    public function ranking(int $year, ?int $month = null): Collection
    {
        return User::role('Phòng Kinh doanh')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function (User $user) use ($year, $month): array {
                $summary = $this->summary($year, $month, $user->id);

                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'signed_value' => $summary['signed_value'],
                    'contract_count' => $summary['contract_count'],
                    'target' => $summary['target'],
                    'target_percent' => $summary['target_percent'],
                    'pipeline' => $summary['pipeline'],
                ];
            })
            ->sortByDesc('signed_value')
            ->values();
    }

    /**
     * @return Collection<int, array{label: string, count: int, value: int}>
     */
    public function contractTypes(int $year, ?int $month = null, ?int $ownerId = null): Collection
    {
        return $this->contractQuery($year, $month, $ownerId)
            ->get()
            ->groupBy(static fn (Contract $contract): string => $contract->type->value)
            ->map(static fn (Collection $contracts): array => [
                'label' => $contracts->first()->type->label(),
                'count' => $contracts->count(),
                'value' => (int) $contracts->sum('value'),
            ])
            ->sortByDesc('value')
            ->values();
    }

    /**
     * @return Collection<int, ContractPaymentSchedule>
     */
    public function overdueSchedules(?int $ownerId = null): Collection
    {
        return ContractPaymentSchedule::query()
            ->with(['contract.customer', 'contract.owner'])
            ->whereNull('cancelled_at')
            ->whereDate('due_date', '<', today())
            ->whereIn('status', [
                PaymentScheduleStatus::Pending->value,
                PaymentScheduleStatus::PartiallyPaid->value,
                PaymentScheduleStatus::Overdue->value,
            ])
            ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                'contract',
                static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
            ))
            ->orderBy('due_date')
            ->limit(20)
            ->get();
    }

    /**
     * @return array<string, int|float>
     */
    public function homeSnapshot(?int $ownerId = null): array
    {
        return $this->summary((int) now()->year, (int) now()->month, $ownerId);
    }

    private function contractQuery(int $year, ?int $month, ?int $ownerId): Builder
    {
        return Contract::query()
            ->whereNotNull('signed_at')
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->whereYear('signed_at', $year)
            ->when($month !== null, static fn (Builder $query) => $query->whereMonth('signed_at', $month))
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId));
    }
}
