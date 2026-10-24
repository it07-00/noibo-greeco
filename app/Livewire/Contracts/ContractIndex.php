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
use App\Models\Department;
use App\Models\Handler;
use App\Models\User;
use App\Services\Contracts\ContractListing;
use App\Services\Contracts\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Hợp đồng')]
final class ContractIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = '';

    public string $filterType = '';

    #[Locked]
    public int $viewingId = 0;

    #[Url]
    public array $filter = [];

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 15;

    public function mount(): void
    {
        Gate::authorize('viewAny', Contract::class);
        if (auth()->user()->hasRole(RoleEnum::Consultant->value) && ! array_key_exists('hide_completed_workflow', $this->filter)) {
            $this->filter['hide_completed_workflow'] = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $contractId): void
    {
        $contract = Contract::query()->findOrFail($contractId);
        Gate::authorize('view', $contract);

        $this->viewingId = $contract->id;
        $this->dispatch('contract-detail:show');
    }

    public function render(ContractService $service): View
    {
        Gate::authorize('viewAny', Contract::class);
        if ($this->viewingId > 0) {
            Gate::authorize('view', Contract::findOrFail($this->viewingId));
        }

        return view('livewire.contracts.contract-index', [
            'contracts' => $service->paginate(
                trim($this->search),
                $this->filterStatus,
                $this->filterType,
                $this->perPage, $this->filter, $this->sortField, $this->sortDirection,
            ),
            'summary' => $service->summary(trim($this->search), $this->filterStatus, $this->filterType, $this->filter),
            'handlers' => Handler::orderBy('name')->get(['id', 'name']),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'staff' => User::whereNull('locked_at')->orderBy('name')->get(['id', 'name']),
            'renewalOptions' => ContractRenewalStatus::options(),
            'voucherOptions' => ContractVoucherStatus::options(),
            'paymentOptions' => PaymentMethod::options(),
            'serviceOptions' => ServiceType::options(),
            'statusOptions' => ContractStatus::options(),
            'typeOptions' => ContractType::options(),
            'detailContract' => $this->viewingId > 0
                ? Contract::query()->with(['customer', 'handler', 'owner', 'quotation', 'services', 'paymentSchedules'])->find($this->viewingId)
                : null,
            'showFinancials' => ! auth()->user()->hasRole(RoleEnum::Consultant->value),
        ]);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updateInlineReportNumber(int $id, string $value): void
    {
        $contract = Contract::findOrFail($id);
        Gate::authorize('update', $contract);
        Validator::make(['reportNumber' => $value], ['reportNumber' => ['nullable', 'string', 'max:100']])->validate();
        $contract->update(['report_number' => trim($value) ?: null]);
    }

    public function updateStatus(int $id, string $value): void
    {
        $contract = Contract::findOrFail($id);
        Gate::authorize('update', $contract);
        Validator::make(['contractStatus' => $value], ['contractStatus' => ['required', Rule::enum(ContractStatus::class)]])->validate();
        $contract->update(['status' => $value]);
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSortField(): void
    {
        $this->resetPage();
    }

    public function updatedSortDirection(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['id', 'created_at', 'signed_at', 'submitted_at', 'report_number', 'contract_number'], true)) {
            return;
        }
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'desc' ? 'asc' : 'desc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('filter', 'search', 'filterStatus', 'filterType');
        $this->resetPage();
    }

    public function exportExcel(ContractListing $listing): StreamedResponse
    {
        Gate::authorize(PermissionEnum::ContractExport->value);
        Gate::authorize('viewAny', Contract::class);
        $query = $listing->ordered($listing->query($this->search, $this->filterStatus, $this->filterType, $this->filter), $this->sortField, $this->sortDirection)
            ->with(['customer', 'owner', 'department', 'handler']);
        $showFinancials = ! auth()->user()->hasRole(RoleEnum::Consultant->value);

        return response()->streamDownload(function () use ($query, $showFinancials): void {
            echo view('exports.contracts', ['contracts' => $query->lazy(200), 'showFinancials' => $showFinancials])->render();
        }, 'hop_dong_'.now()->format('Y_m_d').'.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }
}
