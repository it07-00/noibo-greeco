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
    ): LengthAwarePaginator {
        return Contract::query()
            ->with(['customer', 'owner'])
            ->withCount('paymentSchedules')
            ->withSum([
                'payments as received_amount' => static fn ($query) => $query->whereNull('voided_at'),
            ], 'amount')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('contract_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhereHas(
                            'customer',
                            static fn ($customerQuery) => $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('tax_code', 'like', "%{$search}%"),
                        );
                });
            })
            ->when($status !== '', static fn ($query) => $query->where('status', $status))
            ->when($type !== '', static fn ($query) => $query->where('type', $type))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @return array{total: int, active: int, total_value: int, received: int}
     */
    public function summary(): array
    {
        return [
            'total' => Contract::query()->count(),
            'active' => Contract::query()->where('status', ContractStatus::Active->value)->count(),
            'total_value' => (float) Contract::query()->sum('value'),
            'received' => (float) ContractPayment::query()
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
