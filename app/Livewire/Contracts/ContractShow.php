<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentTermUnit;
use App\Enums\PermissionEnum;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\Department;
use App\Models\User;
use App\Services\Contracts\ContractDocumentService;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\ContractWorkflowService;
use App\Services\Payments\PaymentRecordingService;
use App\Services\Payments\PaymentScheduleService;
use Carbon\CarbonImmutable;
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

#[Layout('layouts.app')]
#[Title('Chi tiết hợp đồng')]
final class ContractShow extends Component
{
    use WithFileUploads;

    public Contract $contract;

    public int $editingScheduleId = 0;

    public string $scheduleName = '';

    public string $schedulePercentage = '';

    public string $scheduleAmount = '';

    public string $scheduleConditionType = '';

    public string $scheduleCustomCondition = '';

    public string $scheduleExpectedTriggerDate = '';

    public string $schedulePaymentTermDays = '';

    public string $schedulePaymentTermUnit = '';

    public string $scheduleDueDate = '';

    public string $scheduleNotes = '';

    public string $returnReason = '';

    public string $paymentPaidAt = '';

    public string $paymentAmount = '';

    public string $paymentMethod = '';

    public string $paymentReference = '';

    public string $paymentNotes = '';

    /**
     * @var list<array{payment_schedule_id: int, amount: string}>
     */
    public array $allocationRows = [];

    public string $contractNumber = '';

    public string $contractTitle = '';

    public string $contractSignedAt = '';

    public string $contractStartsAt = '';

    public string $contractEndsAt = '';

    public string $contractNotes = '';

    public string $contractPaymentMethod = '';

    public string $contractRenewalStatus = '';

    public int $documentRevisionSourceId = 0;

    public string $documentType = '';

    public string $documentTitle = '';

    public string $documentPaymentScheduleId = '';

    public string $documentExpiresAt = '';

    public mixed $documentFile = null;

    public int $reviewingDocumentId = 0;

    public string $documentReviewFeedback = '';

    public function mount(Contract $contract): void
    {
        Gate::authorize('view', $contract);
        $this->contract = $contract;
    }

    public function openContractInfo(): void
    {
        Gate::authorize('update', $this->contract);
        $this->contractNumber = $this->contract->contract_number ?? '';
        $this->contractTitle = $this->contract->title;
        $this->contractSignedAt = $this->contract->signed_at?->toDateString() ?? '';
        $this->contractStartsAt = $this->contract->starts_at?->toDateString() ?? '';
        $this->contractEndsAt = $this->contract->ends_at?->toDateString() ?? '';
        $this->contractNotes = $this->contract->notes ?? '';
        $this->contractPaymentMethod = $this->contract->payment_method?->value ?? '';
        $this->contractRenewalStatus = $this->contract->renewal_status?->value ?? '';
        $this->resetValidation();
        $this->dispatch('contract-info:show');
    }

    public function saveContractInfo(): void
    {
        Gate::authorize('update', $this->contract);

        $validated = $this->validate([
            'contractNumber' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('contracts', 'contract_number')->ignore($this->contract->id),
            ],
            'contractTitle' => ['required', 'string', 'max:191'],
            'contractSignedAt' => ['nullable', 'date'],
            'contractStartsAt' => ['nullable', 'date'],
            'contractEndsAt' => ['nullable', 'date', 'after_or_equal:contractStartsAt'],
            'contractNotes' => ['nullable', 'string', 'max:3000'],
            'contractPaymentMethod' => ['nullable', Rule::enum(PaymentMethod::class)],
            'contractRenewalStatus' => ['nullable', Rule::enum(ContractRenewalStatus::class)],
        ], [
            'contractTitle.required' => 'Vui lòng nhập tên hợp đồng.',
            'contractNumber.unique' => 'Số hợp đồng đã tồn tại.',
            'contractEndsAt.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        $this->contract->update([
            'contract_number' => $validated['contractNumber'] ?: null,
            'title' => trim($validated['contractTitle']),
            'signed_at' => $validated['contractSignedAt'] ?: null,
            'starts_at' => $validated['contractStartsAt'] ?: null,
            'ends_at' => $validated['contractEndsAt'] ?: null,
            'notes' => $validated['contractNotes'] ?: null,
            'payment_method' => $validated['contractPaymentMethod'] ?: null,
            'renewal_status' => $validated['contractRenewalStatus'] ?: ContractRenewalStatus::NotApplicable->value,
        ]);

        $this->contract->refresh();
        $this->dispatch('contract-info:hide');
        $this->successToast('Đã cập nhật hợp đồng');
    }

    public function openScheduleCreate(): void
    {
        Gate::authorize('create', ContractPaymentSchedule::class);
        $this->resetScheduleForm();
        $next = ((int) $this->contract->paymentSchedules()->max('installment_number')) + 1;
        $this->scheduleName = "Đợt {$next}";
        $this->scheduleConditionType = PaymentConditionType::AfterContractSigned->value;
        $this->schedulePaymentTermUnit = PaymentTermUnit::CalendarDays->value;
        $this->dispatch('payment-schedule:show');
    }

    public function openScheduleEdit(int $scheduleId): void
    {
        $schedule = $this->findSchedule($scheduleId);
        Gate::authorize('update', $schedule);

        $this->editingScheduleId = $schedule->id;
        $this->scheduleName = $schedule->name;
        $this->schedulePercentage = $schedule->percentage ?? '';
        $this->scheduleAmount = (string) $schedule->amount;
        $this->scheduleConditionType = $schedule->condition_type->value;
        $this->scheduleCustomCondition = $schedule->custom_condition ?? '';
        $this->scheduleExpectedTriggerDate = $schedule->expected_trigger_date?->toDateString() ?? '';
        $this->schedulePaymentTermDays = $schedule->payment_term_days !== null
            ? (string) $schedule->payment_term_days
            : '';
        $this->schedulePaymentTermUnit = $schedule->payment_term_unit?->value ?? '';
        $this->scheduleDueDate = $schedule->due_date?->toDateString() ?? '';
        $this->scheduleNotes = $schedule->notes ?? '';
        $this->resetValidation();
        $this->dispatch('payment-schedule:show');
    }

    public function saveSchedule(PaymentScheduleService $service): void
    {
        $schedule = $this->editingScheduleId > 0
            ? $this->findSchedule($this->editingScheduleId)
            : null;
        Gate::authorize($schedule ? 'update' : 'create', $schedule ?? ContractPaymentSchedule::class);

        $validated = $this->validate([
            'scheduleName' => ['required', 'string', 'max:191'],
            'schedulePercentage' => ['nullable', 'numeric', 'min:0.01', 'max:100'],
            'scheduleAmount' => ['required', 'integer', 'min:1'],
            'scheduleConditionType' => ['required', Rule::enum(PaymentConditionType::class)],
            'scheduleCustomCondition' => ['nullable', 'string', 'max:2000'],
            'scheduleExpectedTriggerDate' => ['nullable', 'date'],
            'schedulePaymentTermDays' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'schedulePaymentTermUnit' => ['nullable', Rule::enum(PaymentTermUnit::class)],
            'scheduleDueDate' => ['nullable', 'date'],
            'scheduleNotes' => ['nullable', 'string', 'max:2000'],
        ], [
            'scheduleName.required' => 'Vui lòng nhập tên đợt.',
            'scheduleAmount.min' => 'Số tiền phải lớn hơn 0.',
        ]);

        try {
            $service->save($this->contract, [
                'name' => trim($validated['scheduleName']),
                'percentage' => $validated['schedulePercentage'] ?: null,
                'amount' => (int) $validated['scheduleAmount'],
                'condition_type' => $validated['scheduleConditionType'],
                'custom_condition' => $validated['scheduleCustomCondition'] ?: null,
                'expected_trigger_date' => $validated['scheduleExpectedTriggerDate'] ?: null,
                'payment_term_days' => $validated['schedulePaymentTermDays'] !== ''
                    ? (int) $validated['schedulePaymentTermDays']
                    : null,
                'payment_term_unit' => $validated['schedulePaymentTermUnit'] ?: null,
                'due_date' => $validated['scheduleDueDate'] ?: null,
                'notes' => $validated['scheduleNotes'] ?: null,
            ], $schedule);
        } catch (DomainException $exception) {
            $this->addError('scheduleAmount', $exception->getMessage());

            return;
        }

        $this->dispatch('payment-schedule:hide');
        $this->resetScheduleForm();
        $this->successToast('Đã lưu đợt thanh toán');
    }

    public function deleteSchedule(
        int $scheduleId,
        PaymentScheduleService $service,
    ): void {
        $schedule = $this->findSchedule($scheduleId);
        Gate::authorize('update', $schedule);

        try {
            $service->delete($schedule);
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã xóa đợt thanh toán');
    }

    public function submitPaymentPlan(PaymentScheduleService $service): void
    {
        Gate::authorize('create', ContractPaymentSchedule::class);

        try {
            $accountingDepartmentId = Department::query()->where('code', 'TCKT')->value('id');
            $service->submitPlan($this->contract, $accountingDepartmentId);
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã gửi kế hoạch sang Kế toán');
    }

    public function confirmPaymentPlan(PaymentScheduleService $service): void
    {
        Gate::authorize(PermissionEnum::PaymentScheduleConfirm->value);

        try {
            $service->confirmPlan($this->contract, $this->actor());
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Kế toán đã xác nhận lịch thanh toán');
    }

    public function returnPaymentPlan(PaymentScheduleService $service): void
    {
        Gate::authorize(PermissionEnum::PaymentScheduleConfirm->value);
        $this->validate([
            'returnReason' => ['required', 'string', 'max:1000'],
        ], [
            'returnReason.required' => 'Vui lòng nhập nội dung cần Kinh doanh bổ sung.',
        ]);

        $service->returnPlan($this->contract, $this->returnReason);
        $this->returnReason = '';
        $this->dispatch('payment-return:hide');
        $this->successToast('Đã trả kế hoạch về Kinh doanh');
    }

    public function triggerSchedule(
        int $scheduleId,
        PaymentScheduleService $service,
    ): void {
        $schedule = $this->findSchedule($scheduleId);
        Gate::authorize('update', $schedule);
        $service->trigger($schedule, CarbonImmutable::today());
        $this->successToast('Đã xác nhận điều kiện thanh toán');
    }

    public function openPayment(): void
    {
        Gate::authorize('create', ContractPayment::class);
        $this->paymentPaidAt = now()->toDateString();
        $this->paymentAmount = '';
        $this->paymentMethod = PaymentMethod::BankTransfer->value;
        $this->paymentReference = '';
        $this->paymentNotes = '';
        $this->allocationRows = $this->contract->paymentSchedules()
            ->whereNull('cancelled_at')
            ->orderBy('installment_number')
            ->get()
            ->map(static fn (ContractPaymentSchedule $schedule): array => [
                'payment_schedule_id' => $schedule->id,
                'amount' => '0',
            ])
            ->all();
        $this->resetValidation();
        $this->dispatch('payment-record:show');
    }

    public function recordPayment(PaymentRecordingService $service): void
    {
        Gate::authorize('create', ContractPayment::class);

        $validated = $this->validate([
            'paymentPaidAt' => ['required', 'date'],
            'paymentAmount' => ['required', 'integer', 'min:1'],
            'paymentMethod' => ['required', Rule::enum(PaymentMethod::class)],
            'paymentReference' => ['nullable', 'string', 'max:191'],
            'paymentNotes' => ['nullable', 'string', 'max:2000'],
            'allocationRows' => ['array'],
            'allocationRows.*.payment_schedule_id' => ['required', 'integer'],
            'allocationRows.*.amount' => ['required', 'integer', 'min:0'],
        ], [
            'paymentAmount.min' => 'Số tiền nhận phải lớn hơn 0.',
        ]);

        $allocations = collect($validated['allocationRows'])
            ->filter(static fn (array $row): bool => (int) $row['amount'] > 0)
            ->map(static fn (array $row): array => [
                'payment_schedule_id' => (int) $row['payment_schedule_id'],
                'amount' => (int) $row['amount'],
            ])
            ->values()
            ->all();

        try {
            $service->record($this->contract, [
                'paid_at' => $validated['paymentPaidAt'],
                'amount' => (int) $validated['paymentAmount'],
                'payment_method' => $validated['paymentMethod'],
                'reference_number' => $validated['paymentReference'] ?: null,
                'notes' => $validated['paymentNotes'] ?: null,
            ], $allocations, $this->actor());
        } catch (DomainException $exception) {
            $this->addError('paymentAmount', $exception->getMessage());

            return;
        }

        $this->dispatch('payment-record:hide');
        $this->successToast('Đã ghi nhận tiền về');
    }

    public function transitionContract(
        string $target,
        ContractWorkflowService $workflow,
    ): void {
        $status = ContractStatus::from($target);
        $ability = match ($status) {
            ContractStatus::Active => 'activate',
            ContractStatus::Completed => 'complete',
            ContractStatus::Cancelled => 'cancel',
            default => 'update',
        };
        Gate::authorize($ability, $this->contract);

        try {
            $this->contract = $workflow->transition($this->contract, $status);
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã cập nhật trạng thái hợp đồng');
    }

    public function openDocumentCreate(): void
    {
        Gate::authorize('create', ContractDocument::class);
        $this->resetDocumentForm();
        $this->documentType = DocumentType::Contract->value;
        $this->dispatch('contract-document:show');
    }

    public function openDocumentRevision(int $documentId): void
    {
        $document = $this->findDocument($documentId);
        Gate::authorize('create', ContractDocument::class);

        if (! in_array($document->status, [DocumentStatus::RevisionRequired, DocumentStatus::Rejected], true)) {
            $this->errorAlert('Chứng từ này không cần tạo phiên bản sửa đổi.');

            return;
        }

        $this->resetDocumentForm();
        $this->documentRevisionSourceId = $document->id;
        $this->documentType = $document->type->value;
        $this->documentTitle = $document->title;
        $this->documentPaymentScheduleId = $document->payment_schedule_id
            ? (string) $document->payment_schedule_id
            : '';
        $this->documentExpiresAt = $document->expires_at?->toDateString() ?? '';
        $this->dispatch('contract-document:show');
    }

    public function saveDocument(ContractDocumentService $service): void
    {
        Gate::authorize('create', ContractDocument::class);

        $validated = $this->validate([
            'documentType' => ['required', Rule::enum(DocumentType::class)],
            'documentTitle' => ['required', 'string', 'max:191'],
            'documentPaymentScheduleId' => ['nullable', 'integer'],
            'documentExpiresAt' => ['nullable', 'date'],
            'documentFile' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:20480'],
        ], [
            'documentTitle.required' => 'Vui lòng nhập tên chứng từ.',
            'documentFile.required' => 'Vui lòng chọn file chứng từ.',
            'documentFile.mimes' => 'File phải là PDF, Word, Excel hoặc hình ảnh.',
            'documentFile.max' => 'Dung lượng file không được vượt quá 20 MB.',
        ]);

        $superseded = $this->documentRevisionSourceId > 0
            ? $this->findDocument($this->documentRevisionSourceId)
            : null;
        $path = $this->documentFile->store("contracts/{$this->contract->id}/documents", 'local');

        try {
            $service->create($this->contract, [
                'type' => $validated['documentType'],
                'title' => trim($validated['documentTitle']),
                'payment_schedule_id' => $validated['documentPaymentScheduleId'] !== ''
                    ? (int) $validated['documentPaymentScheduleId']
                    : null,
                'expires_at' => $validated['documentExpiresAt'] ?: null,
                'file_path' => $path,
            ], $this->actor(), $superseded);
        } catch (DomainException $exception) {
            Storage::disk('local')->delete($path);
            $this->addError('documentFile', $exception->getMessage());

            return;
        }

        $this->dispatch('contract-document:hide');
        $this->resetDocumentForm();
        $this->successToast('Đã lưu bản nháp chứng từ');
    }

    public function submitDocument(int $documentId, ContractDocumentService $service): void
    {
        Gate::authorize(PermissionEnum::ContractDocumentSubmit->value);
        $document = $this->findDocument($documentId);

        try {
            $service->submit($document, $this->actor());
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã gửi chứng từ sang Kế toán');
    }

    public function startDocumentReview(int $documentId, ContractDocumentService $service): void
    {
        $document = $this->findDocument($documentId);
        Gate::authorize('review', $document);

        try {
            $service->startReview($document, $this->actor());
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã nhận kiểm tra chứng từ');
    }

    public function openDocumentReview(int $documentId): void
    {
        $document = $this->findDocument($documentId);
        Gate::authorize('review', $document);
        $this->reviewingDocumentId = $document->id;
        $this->documentReviewFeedback = '';
        $this->resetValidation();
        $this->dispatch('document-review:show');
    }

    public function reviewDocument(string $decision, ContractDocumentService $service): void
    {
        $document = $this->findDocument($this->reviewingDocumentId);
        Gate::authorize('review', $document);
        $status = DocumentStatus::from($decision);

        if ($status !== DocumentStatus::Approved) {
            $this->validate([
                'documentReviewFeedback' => ['required', 'string', 'max:2000'],
            ], [
                'documentReviewFeedback.required' => 'Vui lòng nhập nội dung phản hồi.',
            ]);
        }

        try {
            $service->review($document, $status, $this->actor(), $this->documentReviewFeedback);
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->dispatch('document-review:hide');
        $this->successToast('Đã cập nhật kết quả chứng từ');
    }

    public function deleteDocument(int $documentId, ContractDocumentService $service): void
    {
        Gate::authorize(PermissionEnum::ContractDocumentSubmit->value);
        $document = $this->findDocument($documentId);

        try {
            $service->deleteDraft($document);
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Đã xóa bản nháp chứng từ');
    }

    public function render(ContractService $service): View
    {
        $this->contract->refresh()->load([
            'customer',
            'quotation',
            'owner',
            'department',
            'services',
            'paymentSchedules' => static fn ($query) => $query
                ->with(['allocations.payment', 'responsibleUser'])
                ->orderBy('installment_number'),
            'payments' => static fn ($query) => $query
                ->with(['allocations.paymentSchedule', 'recorder'])
                ->latest('paid_at'),
            'documents' => static fn ($query) => $query
                ->with(['paymentSchedule', 'submitter', 'reviewer', 'supersedes'])
                ->latest('created_at'),
        ]);

        return view('livewire.contracts.contract-show', [
            'financial' => $service->financialSummary($this->contract),
            'conditionOptions' => PaymentConditionType::options(),
            'termUnitOptions' => PaymentTermUnit::options(),
            'paymentMethodOptions' => PaymentMethod::options(),
            'renewalStatusOptions' => ContractRenewalStatus::options(),
            'canConfirmPlan' => $this->actor()->can(PermissionEnum::PaymentScheduleConfirm->value),
            'canRecordPayment' => $this->actor()->can(PermissionEnum::PaymentRecord->value),
            'canManagePlan' => $this->actor()->can(PermissionEnum::PaymentScheduleManage->value),
            'documentTypeOptions' => DocumentType::options(),
            'canSubmitDocument' => $this->actor()->can(PermissionEnum::ContractDocumentSubmit->value),
            'canReviewDocument' => $this->actor()->can(PermissionEnum::ContractDocumentReview->value),
        ]);
    }

    private function findSchedule(int $scheduleId): ContractPaymentSchedule
    {
        return $this->contract->paymentSchedules()->findOrFail($scheduleId);
    }

    private function findDocument(int $documentId): ContractDocument
    {
        return $this->contract->documents()->findOrFail($documentId);
    }

    private function actor(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function resetScheduleForm(): void
    {
        $this->reset([
            'editingScheduleId',
            'scheduleName',
            'schedulePercentage',
            'scheduleAmount',
            'scheduleConditionType',
            'scheduleCustomCondition',
            'scheduleExpectedTriggerDate',
            'schedulePaymentTermDays',
            'schedulePaymentTermUnit',
            'scheduleDueDate',
            'scheduleNotes',
        ]);
        $this->resetValidation();
    }

    private function resetDocumentForm(): void
    {
        $this->reset([
            'documentRevisionSourceId',
            'documentType',
            'documentTitle',
            'documentPaymentScheduleId',
            'documentExpiresAt',
            'documentFile',
        ]);
        $this->resetValidation();
    }

    private function successToast(string $title): void
    {
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => $title,
            'toast' => true,
            'position' => 'top-end',
            'timer' => 2400,
        ]);
    }

    private function errorAlert(string $message): void
    {
        $this->dispatch('swal:alert', [
            'icon' => 'error',
            'title' => 'Không thể thực hiện',
            'text' => $message,
        ]);
    }
}
