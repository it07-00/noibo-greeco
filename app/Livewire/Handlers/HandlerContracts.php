<?php

declare(strict_types=1);

namespace App\Livewire\Handlers;

use App\Enums\PermissionEnum;
use App\Models\Handler;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Hợp đồng nhà thầu phụ')]
final class HandlerContracts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Handler $handler;

    public string $search = '';

    public function mount(Handler $handler): void
    {
        $this->authorizeAccess();
        $this->handler = $handler;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorizeAccess();
        $search = trim($this->search);

        return view('livewire.handlers.handler-contracts', [
            'contracts' => $this->handler->contracts()->visibleTo(auth()->user())->with('customer')->when($search !== '', fn ($query) => $query
                ->where(fn ($query) => $query->where('contract_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', "%{$search}%"))))
                ->latest('signed_at')->paginate(15),
        ]);
    }

    private function authorizeAccess(): void
    {
        Gate::authorize(PermissionEnum::HandlerView->value);
        Gate::authorize(PermissionEnum::ContractView->value);
    }
}
