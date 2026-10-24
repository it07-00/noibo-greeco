<?php

declare(strict_types=1);

namespace App\Livewire\Handlers;

use App\Enums\PermissionEnum;
use App\Models\Handler;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Nhà thầu phụ')]
final class HandlerIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    #[Locked]
    public ?int $editingId = null;

    public string $name = '';

    public string $phone = '';

    public string $address = '';

    public function mount(): void
    {
        Gate::authorize(PermissionEnum::HandlerView->value);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize(PermissionEnum::HandlerCreate->value);
        $this->reset('editingId', 'name', 'phone', 'address');
        $this->resetValidation();
        $this->dispatch('handler-form:show');
    }

    public function openEdit(int $id): void
    {
        Gate::authorize(PermissionEnum::HandlerUpdate->value);
        $handler = Handler::findOrFail($id);
        $this->editingId = $handler->id;
        $this->name = $handler->name;
        $this->phone = $handler->phone ?? '';
        $this->address = $handler->address ?? '';
        $this->resetValidation();
        $this->dispatch('handler-form:show');
    }

    public function save(): void
    {
        Gate::authorize($this->editingId ? PermissionEnum::HandlerUpdate->value : PermissionEnum::HandlerCreate->value);
        $handler = $this->editingId ? Handler::findOrFail($this->editingId) : new Handler;
        $this->name = trim($this->name);
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('handlers', 'name')->ignore($handler->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
        ], [], ['name' => 'tên nhà thầu phụ', 'phone' => 'số điện thoại', 'address' => 'địa chỉ']);

        $handler->fill($validated)->save();
        $this->dispatch('handler-form:hide');
        $this->reset('editingId', 'name', 'phone', 'address');
        $this->toast('success', 'Đã lưu nhà thầu phụ.');
    }

    public function delete(int $id): void
    {
        Gate::authorize(PermissionEnum::HandlerDelete->value);
        $handler = Handler::findOrFail($id);
        if ($handler->contracts()->withTrashed()->exists()) {
            $this->toast('error', 'Không thể xóa nhà thầu phụ đang gắn với hợp đồng.');

            return;
        }
        $handler->delete();
        $this->resetPage();
        $this->toast('success', 'Đã xóa nhà thầu phụ.');
    }

    public function render(): View
    {
        Gate::authorize(PermissionEnum::HandlerView->value);
        $search = trim($this->search);

        return view('livewire.handlers.handler-index', [
            'handlers' => Handler::withCount(['contracts' => fn ($query) => $query->visibleTo(auth()->user())])->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")))->orderBy('name')->paginate(10),
            'totalHandlers' => Handler::count(),
        ]);
    }

    private function toast(string $icon, string $title): void
    {
        $this->dispatch('swal:alert', ['icon' => $icon, 'title' => $title, 'toast' => true, 'position' => 'top-end', 'timer' => 3000]);
    }
}
