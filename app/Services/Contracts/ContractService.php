<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractPaymentAllocation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ContractService
{
    /**
     * @return LengthAwarePaginator<Contract>
     */
    public function paginate(
        string $search = '',
        string $status = '',
        string $type = '',
        int $perPage = 15,
        array $filters = [],
        string $sort = 'created_at',
        string $direction = 'desc',
    ): LengthAwarePaginator {
        $listing = app(ContractListing::class);

        return $listing->ordered($listing->query($search, $status, $type, $filters), $sort, $direction)
            ->with(['customer', 'owner', 'handler', 'department', 'assignments.user', 'assignments.assigner'])
            ->withCount('paymentSchedules')
            ->withCount(['workflowSteps' => fn ($query) => $query->where('action', 'complete')])
            ->withMax('assignments', 'deadline')
            ->withSum([
                'payments as received_amount' => static fn ($query) => $query->whereNull('voided_at'),
            ], 'amount')
            ->paginate(in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15);
    }

    /**
     * @return array{total: int, active: int, total_value: int, received: int}
     */
    public function summary(string $search = '', string $status = '', string $type = '', array $filters = []): array
    {
        $query = app(ContractListing::class)->query($search, $status, $type, $filters);

        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', ContractStatus::Active->value)->count(),
            'total_value' => (float) (clone $query)->sum('value'),
            'received' => (float) ContractPayment::query()
                ->whereIn('contract_id', (clone $query)->select('id'))
                ->whereNull('voided_at')
                ->sum('amount'),
        ];
    }

    /**
     * @return array{received: float, allocated: float, unallocated: float, outstanding: float}
     */
    public function financialSummary(Contract $contract): array
    {
        $received = (float) $contract->payments()
            ->whereNull('voided_at')
            ->sum('amount');
        $allocated = (float) ContractPaymentAllocation::query()
            ->whereHas(
                'payment',
                static fn ($query) => $query
                    ->where('contract_id', $contract->id)
                    ->whereNull('voided_at'),
            )
            ->sum('allocated_amount');

        return [
            'received' => $received,
            'allocated' => $allocated,
            'unallocated' => max(0.0, $received - $allocated),
            'outstanding' => max(0.0, $contract->value - $allocated),
        ];
    }
}
