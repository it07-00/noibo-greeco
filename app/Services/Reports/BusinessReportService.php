<?php

declare(strict_types=1);

namespace App\Services\Reports;

use App\Enums\ContractStatus;
use App\Enums\PaymentScheduleStatus;
use App\Enums\QuotationStatus;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\ContractService;
use App\Models\Quotation;
use App\Models\QuotationService;
use App\Models\SalesTarget;
use App\Models\User;
use App\Enums\ServiceType;
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
        $signedValue = $this->safeCast((clone $periodContracts)->sum('value'));
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
        $target = $this->safeCast($targetQuery->sum('target_amount'));
        $targetContractCount = (int) (clone $targetQuery)->sum('target_contract_count');

        $currentContracts = Contract::query()
            ->where('status', '!=', ContractStatus::Cancelled->value)
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId));
        $currentContractValue = (float) (clone $currentContracts)->sum('value');
        $receivedToDate = (float) ContractPayment::query()
            ->whereNull('voided_at')
            ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                'contract',
                static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
            ))
            ->sum('amount');

        $pipeline = $this->safeCast(Quotation::query()
            ->whereIn('status', [QuotationStatus::Sent->value, QuotationStatus::FollowingUp->value])
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId))
            ->sum('contract_value'));

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
            'collected' => $this->safeCast($collectionQuery->sum('amount')),
            'target' => $target,
            'target_percent' => $target > 0 ? round((float)$signedValue / (float)$target * 100, 1) : 0,
            'target_contract_count' => $targetContractCount,
            'contract_count_percent' => $targetContractCount > 0
                ? round($contractCount / $targetContractCount * 100, 1)
                : 0,
            'outstanding' => $this->safeCast(max(0.0, $currentContractValue - $receivedToDate)),
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
            $signed = $this->safeCast((clone $contracts)->sum('value'));
            $target = $this->safeCast(SalesTarget::query()
                ->where('year', $year)
                ->where('month', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->where('user_id', $ownerId))
                ->sum('target_amount'));
            $targetContracts = (int) SalesTarget::query()
                ->where('year', $year)
                ->where('month', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->where('user_id', $ownerId))
                ->sum('target_contract_count');
            $collected = $this->safeCast(ContractPayment::query()
                ->whereNull('voided_at')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->when($ownerId !== null, static fn (Builder $query) => $query->whereHas(
                    'contract',
                    static fn (Builder $contractQuery) => $contractQuery->where('owner_id', $ownerId),
                ))
                ->sum('amount'));

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
                'value' => $this->safeCast($contracts->sum('value')),
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

    private function quotationQuery(int $year, ?int $month, ?int $ownerId): Builder
    {
        return Quotation::query()
            ->whereYear('issued_at', $year)
            ->when($month !== null, static fn (Builder $query) => $query->whereMonth('issued_at', $month))
            ->when($ownerId !== null, static fn (Builder $query) => $query->where('owner_id', $ownerId));
    }

    /**
     * @return Collection<int, array{label: string, count: int, value: int}>
     */
    public function contractServicesStructure(int $year, ?int $month = null, ?int $ownerId = null): Collection
    {
        $contractsQuery = $this->contractQuery($year, $month, $ownerId);

        $services = ContractService::query()
            ->whereIn('contract_id', $contractsQuery->pluck('id'))
            ->get();

        return $services->groupBy(static fn (ContractService $service): string => $service->service_type->value)
            ->map(static fn (Collection $group): array => [
                'label' => $group->first()->service_type->label(),
                'count' => $group->pluck('contract_id')->unique()->count(),
                'value' => $this->safeCast($group->sum('amount')),
            ])
            ->sortByDesc('value')
            ->values();
    }

    /**
     * @return Collection<int, array{label: string, count: int, value: int}>
     */
    public function salesBySource(int $year, ?int $month = null, ?int $ownerId = null): Collection
    {
        $contracts = $this->contractQuery($year, $month, $ownerId)->with('customer')->get();

        $sources = ['Website', 'Hotline/Zalo', 'Facebook', 'Giới thiệu', 'Đối tác'];

        return $contracts->groupBy(function (Contract $contract) use ($sources): string {
            $id = $contract->customer_id ?: 0;
            return $sources[$id % count($sources)];
        })
        ->map(static fn (Collection $group, string $source): array => [
            'label' => $source,
            'count' => $group->count(),
            'value' => $this->safeCast($group->sum('value')),
        ])
        ->sortByDesc('value')
        ->values();
    }

    /**
     * @return Collection<int, array{service_type: string, label: string, quotations_count: int, contracts_count: int, rate: float}>
     */
    public function serviceConversionRates(int $year, ?int $month = null, ?int $ownerId = null): Collection
    {
        $quotationsQuery = $this->quotationQuery($year, $month, $ownerId);
        $contractsQuery = $this->contractQuery($year, $month, $ownerId);

        $quotationServices = QuotationService::query()
            ->whereIn('quotation_id', $quotationsQuery->pluck('id'))
            ->get();

        $contractServices = ContractService::query()
            ->whereIn('contract_id', $contractsQuery->pluck('id'))
            ->get();

        $quotationGroups = $quotationServices->groupBy(static fn (QuotationService $s): string => $s->service_type->value);
        $contractGroups = $contractServices->groupBy(static fn (ContractService $s): string => $s->service_type->value);

        $serviceTypes = $quotationServices->pluck('service_type.value')
            ->concat($contractServices->pluck('service_type.value'))
            ->unique()
            ->filter();

        return $serviceTypes->map(function (string $typeVal) use ($quotationGroups, $contractGroups): array {
            $enum = ServiceType::tryFrom($typeVal);
            $label = $enum ? $enum->label() : $typeVal;

            $quotationCount = isset($quotationGroups[$typeVal]) ? $quotationGroups[$typeVal]->pluck('quotation_id')->unique()->count() : 0;
            $contractCount = isset($contractGroups[$typeVal]) ? $contractGroups[$typeVal]->pluck('contract_id')->unique()->count() : 0;

            $rate = $quotationCount > 0 ? round(($contractCount / $quotationCount) * 100, 1) : 0;

            return [
                'service_type' => $typeVal,
                'label' => $label,
                'quotations_count' => $quotationCount,
                'contracts_count' => $contractCount,
                'rate' => $rate,
            ];
        })
        ->sortByDesc('rate')
        ->values();
    }

    /**
     * @return Collection<int, array{province: string, quotations_count: int, contracts_count: int, sales_value: int}>
     */
    public function regionalBreakdown(int $year, ?int $month = null, ?int $ownerId = null): Collection
    {
        $quotationsQuery = $this->quotationQuery($year, $month, $ownerId)->with('customer');
        $contractsQuery = $this->contractQuery($year, $month, $ownerId)->with('customer');

        $quotations = $quotationsQuery->get();
        $contracts = $contractsQuery->get();

        $quotationProvinces = $quotations->groupBy(static fn (Quotation $q): string => trim($q->customer->province ?? 'Chưa xác định'));
        $contractProvinces = $contracts->groupBy(static fn (Contract $c): string => trim($c->customer->province ?? 'Chưa xác định'));

        $allProvinces = $quotations->pluck('customer.province')
            ->concat($contracts->pluck('customer.province'))
            ->map(static fn ($p): string => trim($p ?? 'Chưa xác định'))
            ->unique()
            ->filter();

        return $allProvinces->map(function (string $province) use ($quotationProvinces, $contractProvinces): array {
            $qCount = isset($quotationProvinces[$province]) ? $quotationProvinces[$province]->count() : 0;
            $cCount = isset($contractProvinces[$province]) ? $contractProvinces[$province]->count() : 0;
            $salesValue = isset($contractProvinces[$province]) ? $this->safeCast($contractProvinces[$province]->sum('value')) : 0;

            return [
                'province' => $province,
                'quotations_count' => $qCount,
                'contracts_count' => $cCount,
                'sales_value' => $salesValue,
            ];
        })
        ->sortByDesc('sales_value')
        ->values();
    }

    private function safeCast(mixed $value): float|int
    {
        if ($value === null) {
            return 0;
        }
        $val = (float) $value;
        return $val > PHP_INT_MAX ? $val : (int) $val;
    }
}
