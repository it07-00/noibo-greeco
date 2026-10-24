<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\ContractVoucherStatus;
use App\Enums\PaymentMethod;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Handler;
use App\Models\User;
use App\Services\Contracts\ContractAssignmentService;
use App\Services\Contracts\ContractEditorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Thông tin hợp đồng')]
final class ContractForm extends Component
{
    #[Locked]
    public ?int $contractId = null;

    public array $form = [
        'customer_id' => '', 'handler_id' => '', 'owner_id' => '', 'department_id' => '',
        'contract_number' => '', 'title' => '', 'type' => 'consulting', 'status' => 'draft',
        'signed_at' => '', 'starts_at' => '', 'ends_at' => '', 'submitted_at' => '',
        'value' => 0, 'original_amount' => 0, 'customer_commission' => 0, 'commission_tax' => 0,
        'revenue' => 0, 'payment_percentage' => 100, 'payment_method' => '',
        'contract_drive_url' => '', 'province' => '', 'info_source' => 'MỚI', 'submission_place' => '',
        'report_number' => '', 'voucher_status' => '', 'renewal_status' => 'not_applicable',
        'is_offset' => false, 'has_room_fund' => false, 'is_overdue' => false, 'is_renewal' => false,
        'parent_contract_id' => '', 'notes' => '',
    ];

    public array $serviceRows = [];

    public string $newCustomerName = '';

    public array $createAssignUserIds = [];

    public string $createAssignExternal = '';

    public string $createAssignDeadline = '';

    #[Locked]
    public array $financialBase = ['value' => 0, 'customer_commission' => 0, 'revenue' => 0];

    public function mount(?int $contractId = null): void
    {
        $this->contractId = $contractId;
        $this->authorizeForm();
        $source = $contractId ? Contract::findOrFail($contractId) : null;
        $copyId = request()->integer('copy');
        if (! $source && $copyId > 0) {
            $source = Contract::findOrFail($copyId);
            Gate::authorize('view', $source);
        }
        if ($source) {
            foreach (array_keys($this->form) as $field) {
                $value = $source->{$field};
                $this->form[$field] = $value instanceof \BackedEnum ? $value->value
                    : ($value instanceof \DateTimeInterface ? $value->format('Y-m-d') : ($value ?? ''));
            }
            $this->serviceRows = $source->services->map(fn ($service) => [
                ...($contractId ? ['id' => $service->id] : []),
                'service_type' => $service->service_type->value, 'description' => $service->description ?? '', 'amount' => $service->amount,
            ])->all();
            if (! $contractId) {
                $this->form['contract_number'] = '';
                $this->form['status'] = ContractStatus::Draft->value;
                $this->form['report_number'] = '';
                $this->form['submitted_at'] = '';
            }
        } else {
            $this->form['owner_id'] = auth()->id();
            $this->form['department_id'] = auth()->user()->department_id ?? '';
        }
        if ($this->serviceRows === []) {
            $this->addService();
        }
        $percentage = max(0.01, (float) $this->form['payment_percentage']);
        foreach (array_keys($this->financialBase) as $field) {
            $this->financialBase[$field] = (float) $this->form[$field] * 100 / $percentage;
        }
        if (auth()->user()->hasRole(RoleEnum::Consultant->value)) {
            foreach (['value', 'original_amount', 'customer_commission', 'commission_tax', 'revenue'] as $field) {
                $this->form[$field] = 0;
            }
            foreach ($this->serviceRows as &$row) {
                $row['amount'] = 0;
            }
            $this->financialBase = ['value' => 0, 'customer_commission' => 0, 'revenue' => 0];
        }
    }

    public function updatedForm(mixed $value, string $field): void
    {
        $percentage = max(0, min(100, (float) $this->form['payment_percentage']));
        if ($field === 'payment_percentage') {
            $this->form['payment_percentage'] = $percentage;
            foreach ($this->financialBase as $key => $base) {
                $this->form[$key] = (int) round($base * $percentage / 100);
            }
        } elseif (array_key_exists($field, $this->financialBase) && is_numeric($value)) {
            $this->financialBase[$field] = (float) $value * 100 / ($percentage > 0 ? $percentage : 100);
        }
    }

    public function addService(): void
    {
        $this->serviceRows[] = ['service_type' => '', 'description' => '', 'amount' => 0];
    }

    public function removeService(int $index): void
    {
        unset($this->serviceRows[$index]);
        $this->serviceRows = array_values($this->serviceRows);
    }

    public function save(ContractEditorService $service, ContractAssignmentService $assignments): mixed
    {
        $contract = $this->authorizeForm();
        $this->form['title'] = trim((string) $this->form['title']);
        $rules = [
            'form.title' => ['required', 'string', 'max:191'],
            'form.customer_id' => ['nullable', 'required_without:newCustomerName', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'newCustomerName' => ['nullable', 'required_without:form.customer_id', 'string', 'max:191', 'regex:/\S/u'],
            'form.handler_id' => ['nullable', Rule::exists('handlers', 'id')->whereNull('deleted_at')],
            'form.owner_id' => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'form.department_id' => ['nullable', 'exists:departments,id'],
            'form.contract_number' => ['nullable', 'string', 'max:191', Rule::unique('contracts', 'contract_number')->ignore($this->contractId)],
            'form.type' => ['required', Rule::enum(ContractType::class)],
            'form.status' => ['required', Rule::enum(ContractStatus::class)],
            'form.renewal_status' => ['required', Rule::enum(ContractRenewalStatus::class)],
            'form.voucher_status' => ['nullable', Rule::enum(ContractVoucherStatus::class)],
            'form.payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'form.ends_at' => ['nullable', 'date', 'after_or_equal:form.starts_at'],
            'form.contract_drive_url' => ['nullable', 'url:http,https', 'max:2048'],
            'form.province' => ['nullable', 'string', 'max:100'],
            'form.info_source' => ['nullable', 'string', 'max:255'],
            'form.submission_place' => ['nullable', 'string', 'max:500'],
            'form.report_number' => ['nullable', 'string', 'max:100'],
            'form.notes' => ['nullable', 'string', 'max:3000'],
            'form.parent_contract_id' => ['nullable', 'required_if:form.is_renewal,true', Rule::exists('contracts', 'id')->whereNull('deleted_at')],
            'form.payment_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'serviceRows' => ['required', 'array', 'min:1', 'max:50'],
            'serviceRows.*.id' => ['sometimes', 'integer', 'distinct', Rule::exists('contract_services', 'id')->where('contract_id', $this->contractId ?? 0)],
            'serviceRows.*.service_type' => ['required', Rule::enum(ServiceType::class)],
            'serviceRows.*.description' => ['nullable', 'string', 'max:2000'],
            'serviceRows.*.amount' => ['required', 'integer', 'min:0', 'max:999999999999999'],
        ];
        foreach (['signed_at', 'starts_at', 'submitted_at'] as $field) {
            $rules['form.'.$field] = ['nullable', 'date'];
        }
        foreach (['value', 'original_amount', 'customer_commission', 'commission_tax', 'revenue'] as $field) {
            $rules['form.'.$field] = ['required', 'integer', 'min:0', 'max:999999999999999'];
        }
        foreach (['is_offset', 'has_room_fund', 'is_overdue', 'is_renewal'] as $field) {
            $rules['form.'.$field] = ['boolean'];
        }
        $validated = $this->validate($rules, [], [
            'form.title' => 'tên hợp đồng', 'form.customer_id' => 'khách hàng', 'newCustomerName' => 'tên khách hàng mới',
            'form.handler_id' => 'nhà thầu phụ', 'form.owner_id' => 'người phụ trách', 'form.department_id' => 'phòng ban',
            'form.contract_number' => 'số hợp đồng', 'form.type' => 'loại hợp đồng', 'form.status' => 'trạng thái',
            'form.renewal_status' => 'tình trạng tái ký', 'form.voucher_status' => 'tình trạng chứng từ',
            'form.payment_method' => 'phương thức thanh toán', 'form.ends_at' => 'ngày kết thúc',
            'form.starts_at' => 'ngày bắt đầu', 'form.signed_at' => 'ngày ký', 'form.submitted_at' => 'ngày trình',
            'form.contract_drive_url' => 'liên kết hồ sơ Drive', 'form.province' => 'tỉnh/thành phố',
            'form.info_source' => 'nguồn thông tin', 'form.submission_place' => 'nơi nộp hồ sơ',
            'form.report_number' => 'số báo cáo', 'form.notes' => 'ghi chú', 'form.parent_contract_id' => 'hợp đồng gốc',
            'form.payment_percentage' => 'tỷ lệ thanh toán', 'form.value' => 'giá trị hợp đồng',
            'form.original_amount' => 'giá trị gốc', 'form.customer_commission' => 'hoa hồng',
            'form.commission_tax' => 'thuế hoa hồng', 'form.revenue' => 'doanh số',
            'serviceRows' => 'dịch vụ', 'serviceRows.*.service_type' => 'loại dịch vụ',
            'serviceRows.*.description' => 'nội dung dịch vụ', 'serviceRows.*.amount' => 'giá trị dịch vụ',
        ]);
        $data = array_map(fn ($value) => $value === '' ? null : $value, $validated['form']);
        $saved = DB::transaction(function () use ($service, $assignments, $contract, $data, $validated): Contract {
            $saved = $service->save($contract, $data, $validated['serviceRows'], $this->newCustomerName, auth()->user());
            if ($contract === null && ($this->createAssignUserIds !== [] || trim($this->createAssignExternal) !== '')) {
                $assignments->sync($saved, $this->createAssignUserIds, $this->createAssignExternal, $this->createAssignDeadline, auth()->user());
            }

            return $saved;
        });
        session()->flash('status', 'Đã lưu hợp đồng.');

        return $this->redirectRoute('contracts.show', $saved);
    }

    public function render(): View
    {
        $this->authorizeForm();

        return view('livewire.contracts.contract-form', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'handlers' => Handler::orderBy('name')->get(['id', 'name']),
            'users' => User::whereNull('locked_at')->orderBy('name')->get(['id', 'name']),
            'departments' => Department::orderBy('name')->get(),
            'parentOptions' => Contract::visibleTo(auth()->user())->when($this->contractId, fn ($query) => $query->whereKeyNot($this->contractId))
                ->orderByDesc('id')->get(['id', 'contract_number', 'title']),
            'typeOptions' => ContractType::options(), 'statusOptions' => ContractStatus::options(),
            'renewalOptions' => ContractRenewalStatus::options(), 'voucherOptions' => ContractVoucherStatus::options(),
            'paymentOptions' => PaymentMethod::options(),
            'serviceOptions' => ($type = ContractType::tryFrom($this->form['type'])) ? ServiceType::optionsFor($type) : [],
            'showFinancials' => ! auth()->user()->hasRole(RoleEnum::Consultant->value),
            'canAssign' => auth()->user()->can(PermissionEnum::ContractAssign->value),
            'canChangeOwner' => auth()->user()->can(PermissionEnum::ContractViewAll->value),
        ]);
    }

    private function authorizeForm(): ?Contract
    {
        $contract = $this->contractId ? Contract::findOrFail($this->contractId) : null;
        Gate::authorize($contract ? 'update' : 'create', $contract ?? Contract::class);

        return $contract;
    }
}
