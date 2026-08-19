<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\CustomerType;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\BaoChauCustomerSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CustomerApiController extends Controller
{
    /**
     * Return list of customers for cross-system sync.
     *
     * GET /api/customers?token=xxx
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $apiToken = (string) config('services.noibo.api_token', 'greeco-noibo-secret-2026');

        if ($request->input('token') !== $apiToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $customers = Customer::query()
            ->select([
                'id',
                'name',
                'tax_code',
                'phone',
                'email',
                'billing_address',
                'work_address',
                'contact_name',
                'province',
                'industry',
                'updated_at',
            ])
            ->latest('updated_at')
            ->get()
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'tax_code' => $c->tax_code,
                'phone' => $c->phone,
                'email' => $c->email,
                'address' => $c->billing_address ?: $c->work_address,
                'billing_address' => $c->billing_address,
                'work_address' => $c->work_address,
                'contact_person' => $c->contact_name,
                'contact_name' => $c->contact_name,
                'province' => $c->province,
                'sector' => $c->industry,
                'industry' => $c->industry,
                'updated_at' => $c->updated_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'count' => $customers->count(),
            'data' => $customers,
        ]);
    }

    /**
     * Receive single customer sync from Bao Chau.
     *
     * POST /api/customers/sync
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'tax_code' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'billing_address' => ['nullable', 'string', 'max:500'],
            'work_address' => ['nullable', 'string', 'max:500'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'industry' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'caretaker_name' => ['nullable', 'string', 'max:255'],
            'caretaker_email' => ['nullable', 'string', 'max:100'],
            'caretaker_phone' => ['nullable', 'string', 'max:50'],
            'care_status' => ['nullable', 'string', 'max:50'],
        ]);

        $apiToken = (string) config('services.noibo.api_token', 'greeco-noibo-secret-2026');

        if ($request->input('token') !== $apiToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $taxCode = trim((string) $request->input('tax_code', ''));
        $name = trim((string) $request->input('name'));

        try {
            BaoChauCustomerSyncService::$isSyncing = true;

            $customer = null;

            if (! empty($taxCode)) {
                $customer = Customer::where('tax_code', $taxCode)->first();
            }

            if (! $customer) {
                $customer = Customer::where('name', $name)->first();
            }

            $address = $request->input('billing_address')
                ?: $request->input('work_address')
                ?: $request->input('address');

            $contactName = $request->input('contact_name')
                ?: $request->input('contact_person');

            $industry = $request->input('industry')
                ?: $request->input('sector');

            $attributes = [
                'name' => $name,
                'type' => CustomerType::Organization,
                'tax_code' => ! empty($taxCode) ? $taxCode : ($customer?->tax_code),
                'phone' => $request->input('phone') ?: $customer?->phone,
                'email' => $request->input('email') ?: $customer?->email,
                'billing_address' => $address ?: $customer?->billing_address,
                'work_address' => $address ?: $customer?->work_address,
                'contact_name' => $contactName ?: $customer?->contact_name,
                'province' => $request->input('province') ?: $customer?->province,
                'industry' => $industry ?: $customer?->industry,
            ];

            if ($customer) {
                $customer->update($attributes);
                $action = 'updated';
            } else {
                $customer = Customer::create($attributes);
                $action = 'created';
            }

            return response()->json([
                'success' => true,
                'action' => $action,
                'customer_id' => $customer->id,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'tax_code' => $customer->tax_code,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Customer sync error from Bao Chau', [
                'error' => $e->getMessage(),
                'payload' => $request->except('token'),
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        } finally {
            BaoChauCustomerSyncService::$isSyncing = false;
        }
    }
}