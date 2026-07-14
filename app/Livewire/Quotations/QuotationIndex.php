<?php

declare(strict_types=1);

namespace App\Livewire\Quotations;

use App\Enums\ContractType;
use App\Enums\DocumentType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentTermUnit;
use App\Enums\PermissionEnum;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;
use App\Services\Quotations\QuotationContractCreationService;
use App\Services\Quotations\QuotationService;
use App\Services\Quotations\QuotationToContractService;
use App\Services\Quotations\QuotationWorkflowService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Theo dõi báo giá')]
final class QuotationIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = '';

    public string $filterContractType = '';

    public int $editingId = 0;

    public int $viewingId = 0;

    public int|string $formCustomerId = 0;

    public int|string $formOwnerId = 0;

    public string $formContractType = '';

    public string $formIssuedAt = '';

    public string $formValidUntil = '';

    public string $formNotes = '';

    public string $formWorkingSituation = '';

    public string $formOriginalAmount = '';

    public string $formCustomerCommission = '';

    public string $formCommissionTax = '';

    public string $formContractValue = '';

    public string $formStatus = 'draft';

    public int $convertingQuotationId = 0;

    public string $convertContractNumber = '';

    public string $convertTitle = '';

    public string $convertValue = '';

    public string $convertOriginalAmount = '';

    public string $convertCustomerCommission = '';

    public string $convertCommissionTax = '';

    public string $convertPaymentMethod = '';

    public string $convertSignedAt = '';

    public string $convertStartsAt = '';

    public string $convertEndsAt = '';

    public string $convertNotes = '';

    public $formFile;

    public ?string $existingFilePath = null;

    /**
     * @var list<array<string, mixed>>
     */
    public array $convertPaymentRows = [];

    /**
     * @var list<array<string, mixed>>
     */
    public array $convertDocumentRows = [];

    /**
     * @var list<array{service_type: string, description: string, quantity: string, unit_price: string}>
     */
    public array $serviceRows = [];

    public function mount(): void
    {
        Gate::authorize('viewAny', Quotation::class);
        $this->formOwnerId = (int) Auth::id();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterContractType(): void
    {
        $this->resetPage();
    }

    public function updatedFormContractType(): void
    {
        $this->serviceRows = [];
        $this->addServiceRow();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Quotation::class);
        $this->resetForm();
        $this->formContractType = ContractType::Consulting->value;
        $this->formOwnerId = (int) Auth::id();
        $this->formStatus = QuotationStatus::Draft->value;
        $this->formIssuedAt = now()->toDateString();
        $this->addServiceRow();
        $this->dispatch('quotation-form:show');
    }

    public function openEdit(int $quotationId): void
    {
        $quotation = Quotation::query()->with('services')->findOrFail($quotationId);
        Gate::authorize('update', $quotation);

        $this->editingId = $quotation->id;
        $this->formCustomerId = $quotation->customer_id;
        $this->formOwnerId = $quotation->owner_id ?? (int) Auth::id();
        $this->formContractType = $quotation->contract_type->value;
        /** @var \Illuminate\Support\Carbon|null $issuedAt */
        $issuedAt = $quotation->issued_at;
        /** @var \Illuminate\Support\Carbon|null $validUntil */
        $validUntil = $quotation->valid_until;
        $this->formIssuedAt = $issuedAt?->toDateString() ?? '';
        $this->formValidUntil = $validUntil?->toDateString() ?? '';
        $this->formNotes = $quotation->notes ?? '';
        $this->formWorkingSituation = $quotation->working_situation ?? '';
        $this->formOriginalAmount = (string) $quotation->original_amount;
        $this->formCustomerCommission = (string) $quotation->customer_commission;
        $this->formCommissionTax = (string) $quotation->commission_tax;
        $this->formContractValue = (string) $quotation->contract_value;
        $this->formStatus = $quotation->status->value;
        $this->existingFilePath = $quotation->file_path;
        $this->formFile = null;
        $this->serviceRows = $quotation->services
            ->map(static fn ($service): array => [
                'service_type' => $service->service_type->value,
                'description' => $service->description ?? '',
                'quantity' => (string) (int) $service->quantity,
                'unit_price' => (string) $service->unit_price,
            ])
            ->values()
            ->all();
        $this->resetValidation();
        $this->dispatch('quotation-form:show');
    }

    public function openDetail(int $quotationId): void
    {
        $quotation = Quotation::query()->findOrFail($quotationId);
        Gate::authorize('view', $quotation);

        $this->viewingId = $quotation->id;
        $this->dispatch('quotation-detail:show');
    }

    public function addServiceRow(): void
    {
        $contractType = ContractType::tryFrom($this->formContractType);
        $firstService = $contractType
            ? array_key_first(ServiceType::optionsFor($contractType))
            : '';

        $this->serviceRows[] = [
            'service_type' => (string) $firstService,
            'description' => '',
            'quantity' => '1',
            'unit_price' => '0',
        ];
    }

    public function removeServiceRow(int $index): void
    {
        if (count($this->serviceRows) <= 1) {
            return;
        }

        unset($this->serviceRows[$index]);
        $this->serviceRows = array_values($this->serviceRows);
    }

    public function save(QuotationService $service): void
    {
        $quotation = $this->editingId > 0
            ? Quotation::query()->findOrFail($this->editingId)
            : null;
        Gate::authorize($quotation ? 'update' : 'create', $quotation ?? Quotation::class);

        $allowedServices = ContractType::tryFrom($this->formContractType)
            ? array_keys(ServiceType::optionsFor(ContractType::from($this->formContractType)))
            : [];

        if (! $this->canChooseOwner()) {
            $this->formOwnerId = (int) Auth::id();
        }

        $validated = $this->validate([
            'formCustomerId' => ['required', 'integer', 'exists:customers,id'],
            'formOwnerId' => ['required', 'integer', 'exists:users,id'],
            'formContractType' => ['required', Rule::enum(ContractType::class)],
            'formIssuedAt' => ['nullable', 'date'],
            'formValidUntil' => ['nullable', 'date', 'after_or_equal:formIssuedAt'],
            'formNotes' => ['nullable', 'string', 'max:3000'],
            'formWorkingSituation' => ['nullable', 'string', 'max:3000'],
            'formOriginalAmount' => ['nullable', 'numeric', 'min:0'],
            'formCustomerCommission' => ['nullable', 'numeric', 'min:0'],
            'formCommissionTax' => ['nullable', 'numeric', 'min:0'],
            'formContractValue' => ['nullable', 'numeric', 'min:0'],
            'formStatus' => ['required', Rule::enum(QuotationStatus::class)],
            'formFile' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
            'serviceRows' => ['required', 'array', 'min:1'],
            'serviceRows.*.service_type' => ['required', Rule::in($allowedServices)],
            'serviceRows.*.description' => ['nullable', 'string', 'max:2000'],
            'serviceRows.*.quantity' => ['required', 'integer', 'gt:0'],
            'serviceRows.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'formCustomerId.required' => 'Vui lòng chọn khách hàng.',
            'formValidUntil.after_or_equal' => 'Ngày hết hiệu lực phải sau ngày báo giá.',
            'formFile.max' => 'Dung lượng file không được vượt quá 20 MB.',
            'formFile.mimes' => 'Định dạng file không hỗ trợ.',
            'formStatus.required' => 'Vui lòng chọn trạng thái báo giá.',
            'serviceRows.*.service_type.in' => 'Dịch vụ không thuộc loại hợp đồng.',
            'serviceRows.*.quantity.integer' => 'Số lượng phải là số nguyên.',
            'serviceRows.*.quantity.gt' => 'Số lượng phải lớn hơn 0.',
        ]);

        try {
            $savedQuotation = $service->saveDraft([
                'customer_id' => $validated['formCustomerId'],
                'owner_id' => $validated['formOwnerId'],
                'contract_type' => $validated['formContractType'],
                'issued_at' => $validated['formIssuedAt'] ?: null,
                'valid_until' => $validated['formValidUntil'] ?: null,
                'notes' => $validated['formNotes'] ?: null,
                'working_situation' => $validated['formWorkingSituation'] ?: null,
                'original_amount' => $validated['formOriginalAmount'] !== '' ? (float) $validated['formOriginalAmount'] : null,
                'customer_commission' => $validated['formCustomerCommission'] !== '' ? (float) $validated['formCustomerCommission'] : 0.0,
                'commission_tax' => $validated['formCommissionTax'] !== '' ? (float) $validated['formCommissionTax'] : 0.0,
                'contract_value' => $validated['formContractValue'] !== '' ? (float) $validated['formContractValue'] : null,
                'status' => $validated['formStatus'],
            ], $validated['serviceRows'], Auth::user(), $quotation);

            if ($this->formFile) {
                if ($savedQuotation->file_path && Storage::disk('local')->exists($savedQuotation->file_path)) {
                    Storage::disk('local')->delete($savedQuotation->file_path);
                }

                $path = $this->formFile->store(
                    path: 'quotations/' . $savedQuotation->id,
                    options: 'local',
                );

                $savedQuotation->update(['file_path' => $path]);
            }
        } catch (DomainException $exception) {
            $this->addError('serviceRows', $exception->getMessage());

            return;
        }

        $this->dispatch('quotation-form:hide');
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã lưu báo giá',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2500,
        ]);
        $this->resetForm();
    }

    public function transitionStatus(
        int $quotationId,
        string $target,
        QuotationWorkflowService $workflow,
    ): void {
        $quotation = Quotation::query()->findOrFail($quotationId);
        $status = QuotationStatus::from($target);

        Gate::authorize(
            $status === QuotationStatus::Sent ? 'send' : 'update',
            $quotation,
        );

        try {
            $workflow->transition($quotation, $status);
        } catch (DomainException $exception) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chuyển trạng thái',
                'text' => $exception->getMessage(),
            ]);

            return;
        }

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã cập nhật trạng thái',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }

    public function openConvertModal(int $quotationId): void
    {
        $quotation = Quotation::query()->findOrFail($quotationId);
        Gate::authorize('convert', $quotation);

        $this->convertingQuotationId = $quotation->id;
        $this->convertContractNumber = '';
        $this->convertTitle = 'Hợp đồng - '.$quotation->customer->name;
        $this->convertValue = (string) ($quotation->contract_value > 0 ? $quotation->contract_value : $quotation->total_amount);
        $this->convertOriginalAmount = (string) $quotation->original_amount;
        $this->convertCustomerCommission = (string) $quotation->customer_commission;
        $this->convertCommissionTax = (string) $quotation->commission_tax;
        $this->convertPaymentMethod = PaymentMethod::BankTransfer->value;
        $this->convertSignedAt = now()->toDateString();
        $this->convertStartsAt = now()->toDateString();
        $this->convertEndsAt = now()->addYear()->toDateString();
        $this->convertNotes = '';
        $this->convertPaymentRows = [];
        $this->convertDocumentRows = [];
        $this->addConvertPaymentRow();

        $this->resetValidation();
        $this->dispatch('convert-modal:show');
    }

    public function addConvertPaymentRow(): void
    {
        $isFirst = $this->convertPaymentRows === [];
        $this->convertPaymentRows[] = [
            'name' => 'Đợt '.(count($this->convertPaymentRows) + 1),
            'percentage' => $isFirst ? '100' : '',
            'amount' => $isFirst ? $this->convertValue : '',
            'condition_type' => PaymentConditionType::AfterContractSigned->value,
            'custom_condition' => '',
            'expected_trigger_date' => '',
            'payment_term_days' => '',
            'payment_term_unit' => PaymentTermUnit::CalendarDays->value,
            'due_date' => '',
            'notes' => '',
        ];
    }

    public function updatedConvertValue(): void
    {
        if (count($this->convertPaymentRows) === 1
            && (float) ($this->convertPaymentRows[0]['percentage'] ?? 0) === 100.0) {
            $this->convertPaymentRows[0]['amount'] = $this->convertValue;
        }
    }

    public function removeConvertPaymentRow(int $index): void
    {
        unset($this->convertPaymentRows[$index]);
        $this->convertPaymentRows = array_values($this->convertPaymentRows);

        foreach ($this->convertDocumentRows as &$documentRow) {
            $documentRow['payment_schedule_index'] = '';
        }
        unset($documentRow);
    }

    public function addConvertDocumentRow(): void
    {
        $quotation = Quotation::query()->find($this->convertingQuotationId);
        $this->convertDocumentRows[] = [
            'type' => DocumentType::Contract->value,
            'title' => $quotation ? 'Hợp đồng - '.$quotation->customer->name : '',
            'payment_schedule_index' => '',
            'expires_at' => '',
            'file' => null,
        ];
    }

    public function removeConvertDocumentRow(int $index): void
    {
        unset($this->convertDocumentRows[$index]);
        $this->convertDocumentRows = array_values($this->convertDocumentRows);
    }

    public function saveConversion(QuotationContractCreationService $creator): mixed
    {
        $quotation = Quotation::query()->findOrFail($this->convertingQuotationId);
        Gate::authorize('convert', $quotation);

        $validated = $this->validate([
            'convertContractNumber' => ['nullable', 'string', 'max:191'],
            'convertTitle' => ['required', 'string', 'max:191'],
            'convertValue' => ['required', 'numeric', 'min:1'],
            'convertOriginalAmount' => ['required', 'numeric', 'min:0'],
            'convertCustomerCommission' => ['required', 'numeric', 'min:0'],
            'convertCommissionTax' => ['required', 'numeric', 'min:0'],
            'convertPaymentMethod' => ['nullable', Rule::enum(PaymentMethod::class)],
            'convertSignedAt' => ['nullable', 'date'],
            'convertStartsAt' => ['nullable', 'date'],
            'convertEndsAt' => ['nullable', 'date', 'after_or_equal:convertStartsAt'],
            'convertNotes' => ['nullable', 'string', 'max:3000'],
            'convertPaymentRows' => ['array'],
            'convertPaymentRows.*.name' => ['required', 'string', 'max:191'],
            'convertPaymentRows.*.percentage' => ['nullable', 'numeric', 'min:0.01', 'max:100'],
            'convertPaymentRows.*.amount' => ['required', 'numeric', 'min:1'],
            'convertPaymentRows.*.condition_type' => ['required', Rule::enum(PaymentConditionType::class)],
            'convertPaymentRows.*.custom_condition' => ['nullable', 'string', 'max:2000'],
            'convertPaymentRows.*.expected_trigger_date' => ['nullable', 'date'],
            'convertPaymentRows.*.payment_term_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'convertPaymentRows.*.payment_term_unit' => ['nullable', Rule::enum(PaymentTermUnit::class)],
            'convertPaymentRows.*.due_date' => ['nullable', 'date'],
            'convertPaymentRows.*.notes' => ['nullable', 'string', 'max:2000'],
            'convertDocumentRows' => ['array'],
            'convertDocumentRows.*.type' => ['required', Rule::enum(DocumentType::class)],
            'convertDocumentRows.*.title' => ['required', 'string', 'max:191'],
            'convertDocumentRows.*.payment_schedule_index' => ['nullable', 'integer', 'min:0'],
            'convertDocumentRows.*.expires_at' => ['nullable', 'date'],
            'convertDocumentRows.*.file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'],
        ], [
            'convertTitle.required' => 'Vui lòng nhập tên hợp đồng.',
            'convertValue.required' => 'Vui lòng nhập giá trị hợp đồng.',
            'convertEndsAt.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'convertPaymentRows.*.amount.required' => 'Vui lòng nhập số tiền của đợt thanh toán.',
            'convertDocumentRows.*.file.required' => 'Vui lòng chọn file chứng từ.',
            'convertDocumentRows.*.file.max' => 'Dung lượng file không được vượt quá 20 MB.',
        ]);

        $scheduleRows = collect($validated['convertPaymentRows'])
            ->map(static fn (array $row): array => [
                'name' => trim($row['name']),
                'percentage' => $row['percentage'] !== '' ? (float) $row['percentage'] : null,
                'amount' => (float) $row['amount'],
                'condition_type' => $row['condition_type'],
                'custom_condition' => $row['custom_condition'] ?: null,
                'expected_trigger_date' => $row['expected_trigger_date'] ?: null,
                'payment_term_days' => $row['payment_term_days'] !== '' ? (int) $row['payment_term_days'] : null,
                'payment_term_unit' => $row['payment_term_unit'] ?: null,
                'due_date' => $row['due_date'] ?: null,
                'notes' => $row['notes'] ?: null,
            ])
            ->values()
            ->all();
        $storedPaths = [];
        $documentRows = [];

        foreach ($validated['convertDocumentRows'] as $index => $row) {
            $path = $this->convertDocumentRows[$index]['file']->store(
                "contracts/from-quotation-{$quotation->id}/documents",
                'local',
            );
            $storedPaths[] = $path;
            $documentRows[] = [
                'type' => $row['type'],
                'title' => trim($row['title']),
                'payment_schedule_index' => $row['payment_schedule_index'] !== ''
                    ? (int) $row['payment_schedule_index']
                    : null,
                'expires_at' => $row['expires_at'] ?: null,
                'file_path' => $path,
            ];
        }

        try {
            $contract = $creator->create($quotation, [
                'contract_number' => $validated['convertContractNumber'] ?: null,
                'title' => trim($validated['convertTitle']),
                'value' => (float) $validated['convertValue'],
                'original_amount' => (float) $validated['convertOriginalAmount'],
                'customer_commission' => (float) $validated['convertCustomerCommission'],
                'commission_tax' => (float) $validated['convertCommissionTax'],
                'payment_method' => $validated['convertPaymentMethod'] ?: null,
                'signed_at' => $validated['convertSignedAt'] ?: null,
                'starts_at' => $validated['convertStartsAt'] ?: null,
                'ends_at' => $validated['convertEndsAt'] ?: null,
                'notes' => $validated['convertNotes'] ?: null,
            ], $scheduleRows, $documentRows, Auth::user());
        } catch (DomainException $exception) {
            Storage::disk('local')->delete($storedPaths);
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể tạo hợp đồng',
                'text' => $exception->getMessage(),
            ]);

            return null;
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);

            throw $exception;
        }

        $this->dispatch('convert-modal:hide');

        $this->redirectRoute(
            'contracts.show',
            ['contract' => $contract->id],
            navigate: true,
        );

        return null;
    }

    public function convertToContract(
        int $quotationId,
        QuotationToContractService $converter,
    ): mixed {
        $quotation = Quotation::query()->findOrFail($quotationId);
        Gate::authorize('convert', $quotation);

        try {
            $contract = $converter->convert($quotation);
        } catch (DomainException $exception) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể tạo hợp đồng',
                'text' => $exception->getMessage(),
            ]);

            return null;
        }

        $this->redirectRoute(
            'contracts.show',
            ['contract' => $contract->id],
            navigate: true,
        );

        return null;
    }

    public function delete(int $quotationId): void
    {
        $quotation = Quotation::query()->findOrFail($quotationId);
        Gate::authorize('delete', $quotation);

        $quotation->delete();

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa báo giá',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2200,
        ]);
    }

    public function downloadFile(int $id): mixed
    {
        $quotation = Quotation::findOrFail($id);
        Gate::authorize('view', $quotation);

        if (! $quotation->file_path || ! Storage::disk('local')->exists($quotation->file_path)) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không tìm thấy file báo giá',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2200,
            ]);

            return null;
        }

        $extension = pathinfo($quotation->file_path, PATHINFO_EXTENSION);
        $fileName = 'Bao_gia_' . ($quotation->quotation_number ?: $quotation->id) . '.' . $extension;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->download($quotation->file_path, $fileName);
    }

    public function deleteFile(): void
    {
        if ($this->editingId > 0) {
            $quotation = Quotation::query()->findOrFail($this->editingId);
            Gate::authorize('update', $quotation);

            if ($quotation->file_path && Storage::disk('local')->exists($quotation->file_path)) {
                Storage::disk('local')->delete($quotation->file_path);
            }

            $quotation->update(['file_path' => null]);
            $this->existingFilePath = null;

            $this->dispatch('swal:alert', [
                'icon' => 'success',
                'title' => 'Đã xóa file báo giá',
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2000,
            ]);
        } else {
            $this->formFile = null;
        }
    }

    public function render(QuotationService $service): View
    {
        $selectedContractType = ContractType::tryFrom($this->formContractType);

        return view('livewire.quotations.quotation-index', [
            'quotations' => $service->paginate(
                trim($this->search),
                $this->filterStatus,
                $this->filterContractType,
            ),
            'summary' => $service->summary(),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'tax_code']),
            'salesUsers' => User::role(RoleEnum::Sales->value)->orderBy('name')->get(['id', 'name']),
            'canChooseOwner' => $this->canChooseOwner(),
            'contractTypeOptions' => ContractType::options(),
            'statusOptions' => QuotationStatus::options(),
            'serviceOptions' => $selectedContractType
                ? ServiceType::optionsFor($selectedContractType)
                : [],
            'paymentConditionOptions' => PaymentConditionType::options(),
            'paymentTermUnitOptions' => PaymentTermUnit::options(),
            'documentTypeOptions' => DocumentType::options(),
            'convertPaymentMethodOptions' => PaymentMethod::options(),
            'conversionSource' => $this->convertingQuotationId > 0
                ? Quotation::query()->with('services')->find($this->convertingQuotationId)
                : null,
            'detailQuotation' => $this->viewingId > 0
                ? Quotation::query()->with(['customer', 'owner', 'services', 'contract'])->find($this->viewingId)
                : null,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'formCustomerId',
            'formOwnerId',
            'formContractType',
            'formIssuedAt',
            'formValidUntil',
            'formNotes',
            'formWorkingSituation',
            'formOriginalAmount',
            'formCustomerCommission',
            'formCommissionTax',
            'formContractValue',
            'formStatus',
            'serviceRows',
            'formFile',
            'existingFilePath',
        ]);
        $this->resetValidation();
    }

    private function canChooseOwner(): bool
    {
        return Auth::user()?->can(PermissionEnum::ManagementDashboardView->value) === true;
    }
}
