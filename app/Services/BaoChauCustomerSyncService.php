<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BaoChauCustomerSyncService
{
    /**
     * Flag to prevent infinite sync loops when processing incoming webhooks.
     */
    public static bool $isSyncing = false;

    private string $apiUrl;

    private string $apiToken;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('services.noibo.api_url', 'https://noibobaochau.me'), '/');
        $this->apiToken = (string) config('services.noibo.api_token', 'greeco-noibo-secret-2026');
    }

    /**
     * Send a single customer to Bao Chau.
     */
    public function syncCustomerToBaoChau(Customer $customer): bool
    {
        if (self::$isSyncing) {
            return false;
        }

        if (empty($this->apiUrl) || empty($this->apiToken)) {
            Log::warning('Bao Chau API is not configured for customer sync');

            return false;
        }

        try {
            $payload = [
                'token' => $this->apiToken,
                'name' => $customer->name,
                'tax_code' => $customer->tax_code,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->billing_address ?: $customer->work_address,
                'billing_address' => $customer->billing_address,
                'work_address' => $customer->work_address,
                'contact_name' => $customer->contact_name,
                'contact_person' => $customer->contact_name,
                'province' => $customer->province,
                'industry' => $customer->industry,
                'sector' => $customer->industry,
                'is_ghg_inventory' => (bool) $customer->is_ghg_inventory,
                'is_energy_audit' => (bool) $customer->is_energy_audit,
                'appendix' => $customer->appendix,
                'system_source' => $customer->system_source ?? 'greeco',
                'caretaker_name' => $customer->caretaker?->name,
                'caretaker_email' => $customer->caretaker?->email,
                'caretaker_phone' => $customer->caretaker?->phone,
                'care_status' => $customer->care_status,
            ];

            $response = Http::timeout(5)
                ->post("{$this->apiUrl}/api/customers/sync", $payload);

            if (! $response->successful()) {
                Log::warning('Bao Chau customer sync failed', [
                    'customer_id' => $customer->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $e) {
            Log::warning('Bao Chau customer sync connection error', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Pull customers from Bao Chau API and save locally.
     *
     * @return array{created: int, updated: int, errors: int}
     */
    public function pullFromBaoChau(): array
    {
        $stats = ['created' => 0, 'updated' => 0, 'errors' => 0];

        if (empty($this->apiUrl) || empty($this->apiToken)) {
            Log::warning('Bao Chau API is not configured');

            return $stats;
        }

        try {
            $response = Http::timeout(15)
                ->get("{$this->apiUrl}/api/customers", [
                    'token' => $this->apiToken,
                ]);

            if (! $response->successful()) {
                Log::warning('Failed to fetch customers from Bao Chau', [
                    'status' => $response->status(),
                ]);
                $stats['errors']++;

                return $stats;
            }

            $customers = $response->json('data', []);

            self::$isSyncing = true;

            foreach ($customers as $item) {
                try {
                    $taxCode = trim((string) ($item['tax_code'] ?? ''));
                    $name = trim((string) ($item['name'] ?? ''));

                    if (empty($name)) {
                        continue;
                    }

                    $customer = null;

                    if (! empty($taxCode)) {
                        $customer = Customer::where('tax_code', $taxCode)->first();
                    }

                    if (! $customer) {
                        $customer = Customer::where('name', $name)->first();
                    }

                    $caretakerId = null;
                    if (! empty($item['caretaker_email'])) {
                        $caretakerId = User::where('email', $item['caretaker_email'])->value('id');
                    }
                    if (! $caretakerId && ! empty($item['caretaker_name'])) {
                        $caretakerId = User::where('name', $item['caretaker_name'])->value('id');
                    }

                    $address = $item['address'] ?? $item['billing_address'] ?? $item['work_address'] ?? $customer?->billing_address;
                    $contactName = $item['contact_person'] ?? $item['contact_name'] ?? $customer?->contact_name;
                    $industry = $item['sector'] ?? $item['industry'] ?? $customer?->industry;

                    $data = [
                        'name' => $name,
                        'type' => CustomerType::Organization,
                        'tax_code' => ! empty($taxCode) ? $taxCode : ($customer?->tax_code),
                        'phone' => $item['phone'] ?? $customer?->phone,
                        'email' => $item['email'] ?? $customer?->email,
                        'billing_address' => $address,
                        'work_address' => $address,
                        'contact_name' => $contactName,
                        'province' => $item['province'] ?? $customer?->province,
                        'industry' => $industry,
                        'system_source' => $customer?->system_source ?: ($item['system_source'] ?? 'baochau'),
                    ];

                    if (isset($item['is_ghg_inventory'])) {
                        $data['is_ghg_inventory'] = (bool) $item['is_ghg_inventory'];
                    }
                    if (isset($item['is_energy_audit'])) {
                        $data['is_energy_audit'] = (bool) $item['is_energy_audit'];
                    }
                    if (isset($item['appendix'])) {
                        $data['appendix'] = $item['appendix'];
                    }

                    if ($caretakerId) {
                        $data['caretaker_id'] = $caretakerId;
                    }
                    if (! empty($item['care_status'])) {
                        $data['care_status'] = $item['care_status'];
                    }

                    if ($customer) {
                        $customer->update($data);
                        $stats['updated']++;
                    } else {
                        Customer::create($data);
                        $stats['created']++;
                    }
                } catch (Throwable $e) {
                    Log::warning('Error saving pulled customer from Bao Chau', [
                        'name' => $item['name'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                    $stats['errors']++;
                }
            }
        } catch (Throwable $e) {
            Log::warning('Bao Chau customer pull connection error', [
                'error' => $e->getMessage(),
            ]);
            $stats['errors']++;
        } finally {
            self::$isSyncing = false;
        }

        return $stats;
    }
}