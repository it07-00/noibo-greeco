<?php

declare(strict_types=1);

namespace App\Livewire\Customers;

use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Khách hàng')]
final class CustomerIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $typeFilter = '';

    public string $sourceFilter = '';

    public string $regulatoryFilter = '';

    public string $caretakerFilter = '';

    public string $provinceFilter = '';

    public int $editingId = 0;

    public string $name = '';

    public string $customerType = CustomerType::Organization->value;

    public string $taxCode = '';

    public string $contactName = '';

    public string $email = '';

    public string $phone = '';

    public string $billingAddress = '';

    public string $workAddress = '';

    public string $province = '';

    public string $industry = '';

    public string $notes = '';

    public string $caretakerId = '';

    public string $careStatus = '';

    public bool $isGhgInventory = false;

    public bool $isEnergyAudit = false;

    public string $appendix = '';

    public string $systemSource = 'greeco';

    public function mount(): void
    {
        Gate::authorize('viewAny', Customer::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSourceFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRegulatoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCaretakerFilter(): void
    {
        $this->resetPage();
    }

    public function updatedProvinceFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Customer::class);
        $this->resetForm();
        $this->caretakerId = (string) (auth()->id() ?? '');
        $this->careStatus = \App\Enums\CustomerCareStatus::NOT_CONTACTED->value;
        $this->dispatch('customer-form:show');
    }

    public function openEdit(int $customerId): void
    {
        $customer = Customer::query()->findOrFail($customerId);
        Gate::authorize('update', $customer);

        $this->editingId = $customer->id;
        $this->name = $customer->name;
        $this->customerType = $customer->type->value;
        $this->taxCode = $customer->tax_code ?? '';
        $this->contactName = $customer->contact_name ?? '';
        $this->email = $customer->email ?? '';
        $this->phone = $customer->phone ?? '';
        $this->billingAddress = $customer->billing_address ?? '';
        $this->workAddress = $customer->work_address ?? '';
        $this->province = $customer->province ?? '';
        $this->industry = $customer->industry ?? '';
        $this->notes = $customer->notes ?? '';
        $this->caretakerId = $customer->caretaker_id ? (string) $customer->caretaker_id : '';
        $this->careStatus = (string) ($customer->care_status ?? '');
        $this->isGhgInventory = (bool) $customer->is_ghg_inventory;
        $this->isEnergyAudit = (bool) $customer->is_energy_audit;
        $this->appendix = (string) ($customer->appendix ?? '');
        $this->systemSource = (string) ($customer->system_source ?? 'greeco');
        $this->resetValidation();
        $this->dispatch('customer-form:show');
    }

    public function lookupTaxCode(): void
    {
        if ($this->customerType !== CustomerType::Organization->value) {
            $this->addError('taxCode', 'Chỉ tra cứu mã số thuế cho khách hàng tổ chức.');

            return;
        }

        if (empty($this->taxCode)) {
            $this->addError('taxCode', 'Vui lòng nhập mã số thuế để tra cứu.');

            return;
        }

        try {
            $response = Http::get("https://api.vietqr.io/v2/business/{$this->taxCode}");
            $data = $response->json();

            if (($data['code'] ?? null) === '00') {
                $company = $data['data'];

                $this->name = $company['name'] ?? $this->name;
                $this->billingAddress = $company['address'] ?? $this->billingAddress;

                $address = $company['address'] ?? '';
                if ($address !== '') {
                    $parts = array_map('trim', explode(',', $address));
                    $lastPart = end($parts);

                    if (in_array(strtolower($lastPart), ['việt nam', 'vietnam']) && count($parts) > 1) {
                        array_pop($parts);
                        $lastPart = end($parts);
                    }

                    $this->province = $lastPart;
                }

                $this->dispatch('swal:alert', [
                    'icon' => 'success',
                    'title' => 'Đã tải thông tin doanh nghiệp',
                    'toast' => true,
                    'position' => 'top-end',
                    'timer' => 2500,
                ]);
            } else {
                $this->addError('taxCode', $data['desc'] ?? 'Không tìm thấy thông tin doanh nghiệp.');
            }
        } catch (\Throwable $e) {
            $this->addError('taxCode', 'Lỗi khi kết nối tới dịch vụ tra cứu.');
        }
    }

    public function save(CustomerService $service): void
    {
        $customer = $this->editingId > 0
            ? Customer::query()->findOrFail($this->editingId)
            : null;
        Gate::authorize($customer ? 'update' : 'create', $customer ?? Customer::class);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:191'],
            'customerType' => ['required', Rule::enum(CustomerType::class)],
            'taxCode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('customers', 'tax_code')->ignore($customer?->id),
            ],
            'contactName' => ['nullable', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:30'],
            'billingAddress' => ['nullable', 'string', 'max:2000'],
            'workAddress' => ['nullable', 'string', 'max:2000'],
            'province' => ['nullable', 'string', 'max:191'],
            'industry' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'caretakerId' => ['nullable', 'string'],
            'careStatus' => ['nullable', 'string', 'max:50'],
            'isGhgInventory' => ['boolean'],
            'isEnergyAudit' => ['boolean'],
            'appendix' => ['nullable', 'string', 'max:191'],
            'systemSource' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Vui lòng nhập tên khách hàng.',
            'taxCode.unique' => 'Mã số thuế đã tồn tại.',
            'email.email' => 'Email không đúng định dạng.',
        ]);

        $caretakerId = filled($validated['caretakerId'] ?? null)
            ? (int) $validated['caretakerId']
            : ($this->editingId === 0 ? auth()->id() : ($customer?->caretaker_id));

        $caretakerName = $caretakerId
            ? User::where('id', $caretakerId)->value('name')
            : ($customer?->caretaker_name);

        $careStatus = $validated['careStatus']
            ?: ($this->editingId === 0 ? \App\Enums\CustomerCareStatus::NOT_CONTACTED->value : ($customer?->care_status));

        $service->save([
            'name' => trim($validated['name']),
            'type' => $validated['customerType'],
            'tax_code' => $validated['customerType'] === CustomerType::Organization->value
                ? ($validated['taxCode'] ?: null)
                : null,
            'contact_name' => $validated['customerType'] === CustomerType::Organization->value
                ? ($validated['contactName'] ?: null)
                : null,
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'billing_address' => $validated['billingAddress'] ?: null,
            'work_address' => $validated['workAddress'] ?: null,
            'province' => $validated['province'] ?: null,
            'industry' => $validated['industry'] ?: null,
            'notes' => $validated['notes'] ?: null,
            'caretaker_id' => $caretakerId,
            'caretaker_name' => $caretakerName,
            'care_status' => $careStatus,
            'is_ghg_inventory' => (bool) ($validated['isGhgInventory'] ?? false),
            'is_energy_audit' => (bool) ($validated['isEnergyAudit'] ?? false),
            'appendix' => $validated['appendix'] ?: null,
            'system_source' => $validated['systemSource'] ?: 'greeco',
        ], $customer);

        $this->dispatch('customer-form:hide');
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã lưu khách hàng',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2500,
        ]);
        $this->resetForm();
    }

    public function render(CustomerService $service): View
    {
        $users = Cache::remember('customers.filter_users', 600, fn () => 
            User::query()->select('id', 'name', 'email')->orderBy('name')->get()
        );

        $caretakerFilterOptions = Cache::remember('customers.filter_caretakers', 600, function () use ($users) {
            $externalCaretakers = Customer::query()
                ->whereNotNull('caretaker_name')
                ->where('caretaker_name', '!=', '')
                ->distinct()
                ->pluck('caretaker_name')
                ->filter(fn ($name) => ! $users->contains('name', $name))
                ->values()
                ->all();

            $options = [];
            foreach ($users as $user) {
                $options[] = [
                    'value' => (string) $user->id,
                    'label' => $user->name,
                ];
            }
            foreach ($externalCaretakers as $name) {
                $options[] = [
                    'value' => 'name:' . $name,
                    'label' => $name,
                ];
            }

            return $options;
        });

        $provinces = Cache::remember('customers.filter_provinces', 600, fn () => 
            Customer::query()
                ->whereNotNull('province')
                ->where('province', '!=', '')
                ->distinct()
                ->orderBy('province')
                ->pluck('province')
                ->all()
        );

        return view('livewire.customers.customer-index', [
            'customers' => $service->paginate(
                trim($this->search),
                $this->typeFilter,
                $this->sourceFilter,
                $this->regulatoryFilter,
                $this->caretakerFilter,
                $this->provinceFilter
            ),
            'customerTypes' => CustomerType::options(),
            'provinces' => $provinces,
            'users' => $users,
            'caretakerFilterOptions' => $caretakerFilterOptions,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'customerType',
            'taxCode',
            'contactName',
            'email',
            'phone',
            'billingAddress',
            'workAddress',
            'province',
            'industry',
            'notes',
            'caretakerId',
            'careStatus',
            'isGhgInventory',
            'isEnergyAudit',
            'appendix',
        ]);
        $this->customerType = CustomerType::Organization->value;
        $this->systemSource = 'greeco';
        $this->resetValidation();
    }
}