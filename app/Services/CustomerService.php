<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CustomerService
{
    /**
     * @return LengthAwarePaginator<Customer>
     */
    public function paginate(string $search = '', string $type = '', int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()
            ->with(['courses' => fn ($query) => $query->orderBy('starts_at')->orderBy('name')])
            ->withCount(['quotations', 'contracts'])
            ->when(in_array($type, CustomerType::values(), true), fn ($query) => $query->where('type', $type))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('tax_code', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data, ?Customer $customer = null): Customer
    {
        if ($customer === null) {
            return Customer::query()->create($data);
        }

        $customer->update($data);

        return $customer->refresh();
    }
}
