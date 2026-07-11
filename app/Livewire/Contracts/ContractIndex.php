<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Models\Contract;
use App\Services\Contracts\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Hợp đồng')]
final class ContractIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = '';

    public string $filterType = '';

    public int $viewingId = 0;

    public function mount(): void
    {
        Gate::authorize('viewAny', Contract::class);
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
        return view('livewire.contracts.contract-index', [
            'contracts' => $service->paginate(
                trim($this->search),
                $this->filterStatus,
                $this->filterType,
            ),
            'summary' => $service->summary(),
            'statusOptions' => ContractStatus::options(),
            'typeOptions' => ContractType::options(),
            'detailContract' => $this->viewingId > 0
                ? Contract::query()->with(['customer', 'owner', 'quotation', 'services', 'paymentSchedules'])->find($this->viewingId)
                : null,
            'showFinancials' => !auth()->user()->hasRole(\App\Enums\RoleEnum::Consultant->value),
        ]);
    }
}
