<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Customer;
use App\Services\BaoChauCustomerSyncService;

final class CustomerObserver
{
    public function __construct(
        private readonly BaoChauCustomerSyncService $syncService
    ) {}

    /**
     * Handle the Customer "saved" event.
     */
    public function saved(Customer $customer): void
    {
        \Illuminate\Support\Facades\Cache::forget('customers.filter_provinces');
        \Illuminate\Support\Facades\Cache::forget('customers.filter_caretakers');
        \Illuminate\Support\Facades\Cache::forget('customers.filter_users');

        if (BaoChauCustomerSyncService::$isSyncing) {
            return;
        }

        dispatch(function () use ($customer): void {
            $this->syncService->syncCustomerToBaoChau($customer);
        })->afterResponse();
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        \Illuminate\Support\Facades\Cache::forget('customers.filter_provinces');
        \Illuminate\Support\Facades\Cache::forget('customers.filter_caretakers');
    }
}