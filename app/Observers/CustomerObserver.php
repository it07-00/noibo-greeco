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
        if (BaoChauCustomerSyncService::$isSyncing) {
            return;
        }

        $this->syncService->syncCustomerToBaoChau($customer);
    }
}