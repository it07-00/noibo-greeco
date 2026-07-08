<?php

declare(strict_types=1);

namespace App\Livewire\Commissions;

use App\Enums\CommissionRequestStatus;
use App\Enums\ContractType;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\CommissionRequest;
use App\Models\User;
use App\Services\CommissionRequestService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Yêu cầu chi hoa hồng')]
final class CommissionRequestIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $paymentBillFile = null;

    public int $payingRequestId = 0;

    public string $search = '';

    public string $statusFilter = '';

    public string $contractTypeFilter = '';

    public string $requestMonthFilter = '';

    public string $requesterFilter = '';

    public int $perPage = 10;

    public int $viewingRequestId = 0;

    public int $rejectingRequestId = 0;

    public string $rejectReason = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedContractTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRequestMonthFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRequesterFilter(): void
    {
        $this->resetPage();
    }

    public function viewRequest(int $id): void
    {
        Gate::authorize(PermissionEnum::CommissionView->value);
        $request = $this->visibleQuery()->findOrFail($id);
        $this->viewingRequestId = $request->id;
        $this->dispatch('commission-view:show');
    }

    public function closeView(): void
    {
        $this->viewingRequestId = 0;
        $this->dispatch('commission-view:hide');
    }

    public function approve(int $id, CommissionRequestService $service): void
    {
        Gate::authorize(PermissionEnum::CommissionApprove->value);

        try {
            $service->approve(CommissionRequest::query()->findOrFail($id), $this->actor());
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->successToast('Kế toán đã duyệt yêu cầu chi hoa hồng');
    }

    public function startPay(int $id): void
    {
        Gate::authorize(PermissionEnum::CommissionPay->value);
        $this->payingRequestId = $id;
        $this->paymentBillFile = null;
        $this->resetValidation('paymentBillFile');
        $this->dispatch('commission-pay:show');
    }

    public function closePayModal(): void
    {
        $this->payingRequestId = 0;
        $this->paymentBillFile = null;
        $this->dispatch('commission-pay:hide');
    }

    public function confirmPay(CommissionRequestService $service): void
    {
        Gate::authorize(PermissionEnum::CommissionPay->value);
        $this->validate([
            'paymentBillFile' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'paymentBillFile.required' => 'Vui lòng tải lên hóa đơn thanh toán.',
            'paymentBillFile.mimes' => 'Hóa đơn phải là định dạng PDF, JPG, JPEG hoặc PNG.',
            'paymentBillFile.max' => 'Dung lượng hóa đơn không được vượt quá 10 MB.',
        ]);

        try {
            $request = CommissionRequest::query()->findOrFail($this->payingRequestId);
            $path = $this->paymentBillFile->store("commissions/{$request->id}", 'local');
            $service->markPaid($request, $path, $this->actor());
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->payingRequestId = 0;
        $this->paymentBillFile = null;
        $this->dispatch('commission-pay:hide');
        $this->successToast('Đã xác nhận chi và lưu hóa đơn thanh toán');
    }

    public function downloadBill(int $id): mixed
    {
        Gate::authorize(PermissionEnum::CommissionView->value);
        $request = $this->visibleQuery()->findOrFail($id);

        if (! $request->payment_bill_path || ! Storage::disk('local')->exists($request->payment_bill_path)) {
            $this->errorAlert('Không tìm thấy file hóa đơn thanh toán.');

            return null;
        }

        $extension = pathinfo($request->payment_bill_path, PATHINFO_EXTENSION);
        $fileName = 'Hoa_don_chi_hoa_hong_HD_' . ($request->contract?->contract_number ?: $request->contract_id) . '.' . $extension;

        return Storage::disk('local')->download($request->payment_bill_path, $fileName);
    }

    public function startReject(int $id): void
    {
        Gate::authorize(PermissionEnum::CommissionApprove->value);
        $this->rejectingRequestId = $id;
        $this->rejectReason = '';
        $this->resetValidation('rejectReason');
        $this->dispatch('commission-reject:show');
    }

    public function reject(CommissionRequestService $service): void
    {
        Gate::authorize(PermissionEnum::CommissionApprove->value);
        $this->validate([
            'rejectReason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rejectReason.required' => 'Vui lòng nhập lý do từ chối.',
            'rejectReason.min' => 'Lý do từ chối cần tối thiểu 5 ký tự.',
        ]);

        try {
            $service->reject(
                CommissionRequest::query()->findOrFail($this->rejectingRequestId),
                $this->rejectReason,
                $this->actor(),
            );
        } catch (DomainException $exception) {
            $this->errorAlert($exception->getMessage());

            return;
        }

        $this->rejectingRequestId = 0;
        $this->rejectReason = '';
        $this->dispatch('commission-reject:hide');
        $this->successToast('Đã từ chối yêu cầu chi hoa hồng');
    }

    public function delete(int $id): void
    {
        $request = $this->visibleQuery()->findOrFail($id);
        $actor = $this->actor();
        abort_unless(
            $request->user_id === $actor->id || $actor->can(PermissionEnum::CommissionDelete->value),
            403,
        );

        if (in_array($request->status, [CommissionRequestStatus::Approved, CommissionRequestStatus::Paid], true)) {
            $this->errorAlert('Không thể xóa yêu cầu đã duyệt hoặc đã chi.');

            return;
        }

        $request->delete();
        $this->successToast('Đã xóa yêu cầu chi hoa hồng');
    }

    public function rejectionReason(?string $notes): string
    {
        return $notes && str_contains($notes, 'Lý do từ chối (kế toán):')
            ? trim(Str::afterLast($notes, 'Lý do từ chối (kế toán):'))
            : '';
    }

    public function render(): View
    {
        Gate::authorize(PermissionEnum::CommissionView->value);

        $query = $this->visibleQuery()->with(['contract.customer', 'requester', 'processor']);
        $this->applyFilters($query);

        $summaryQuery = $this->visibleQuery();
        $this->applyFilters($summaryQuery);

        $statusRows = (clone $summaryQuery)
            ->selectRaw('status, count(*) as count_rows, coalesce(sum(amount), 0) as total_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $summary = [
            'total' => (int) $statusRows->sum('count_rows'),
            'estimated' => (int) ($statusRows->get(CommissionRequestStatus::Estimated->value)?->count_rows ?? 0),
            'approved' => (int) ($statusRows->get(CommissionRequestStatus::Approved->value)?->count_rows ?? 0),
            'paid' => (int) ($statusRows->get(CommissionRequestStatus::Paid->value)?->count_rows ?? 0),
            'rejected' => (int) ($statusRows->get(CommissionRequestStatus::Rejected->value)?->count_rows ?? 0),
            'amount' => (int) $statusRows->sum('total_amount'),
            'paid_amount' => (int) ($statusRows->get(CommissionRequestStatus::Paid->value)?->total_amount ?? 0),
        ];

        $viewingRequest = $this->viewingRequestId > 0
            ? $this->visibleQuery()->with(['contract.customer', 'requester', 'processor'])->find($this->viewingRequestId)
            : null;

        return view('livewire.commissions.commission-request-index', [
            'requests' => $query->latest()->paginate($this->perPage),
            'statusOptions' => CommissionRequestStatus::options(),
            'contractTypes' => ContractType::options(),
            'summary' => $summary,
            'requesters' => $this->requesters(),
            'viewingRequest' => $viewingRequest,
            'canCreate' => $this->actor()->can(PermissionEnum::CommissionCreate->value),
            'canApprove' => $this->actor()->can(PermissionEnum::CommissionApprove->value),
            'canPay' => $this->actor()->can(PermissionEnum::CommissionPay->value),
        ]);
    }

    private function visibleQuery(): Builder
    {
        $query = CommissionRequest::query();

        if (! $this->canViewAll($this->actor())) {
            $query->where('user_id', $this->actor()->id);
        }

        return $query;
    }

    private function applyFilters(Builder $query): void
    {
        $query
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('receiver_name', 'like', '%'.$this->search.'%')
                        ->orWhere('bank_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('contract', function (Builder $contractQuery): void {
                            $contractQuery->where('contract_number', 'like', '%'.$this->search.'%')
                                ->orWhere('title', 'like', '%'.$this->search.'%')
                                ->orWhereHas('customer', fn (Builder $customerQuery) => $customerQuery->where('name', 'like', '%'.$this->search.'%'));
                        });
                });
            })
            ->when($this->statusFilter !== '', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->contractTypeFilter !== '', fn (Builder $query) => $query->whereHas('contract', fn (Builder $contractQuery) => $contractQuery->where('type', $this->contractTypeFilter)))
            ->when($this->requestMonthFilter !== '' && preg_match('/^\d{4}-\d{2}$/', $this->requestMonthFilter), function (Builder $query): void {
                [$year, $month] = explode('-', $this->requestMonthFilter);
                $query->whereYear('created_at', (int) $year)->whereMonth('created_at', (int) $month);
            })
            ->when($this->requesterFilter !== '', fn (Builder $query) => $query->where('user_id', (int) $this->requesterFilter));
    }

    private function requesters()
    {
        return User::query()
            ->whereIn('id', CommissionRequest::query()->select('user_id')->distinct())
            ->when(! $this->canViewAll($this->actor()), fn (Builder $query) => $query->whereKey($this->actor()->id))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function canViewAll(User $user): bool
    {
        return $user->hasAnyRole([
            RoleEnum::SuperAdmin->value,
            RoleEnum::Director->value,
            RoleEnum::Accountant->value,
            RoleEnum::IT->value,
        ]);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function successToast(string $title): void
    {
        $this->dispatch('swal:alert', ['icon' => 'success', 'title' => $title, 'toast' => true, 'position' => 'top-end', 'timer' => 2200]);
    }

    private function errorAlert(string $message): void
    {
        $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Không thể thực hiện', 'text' => $message]);
    }
}
