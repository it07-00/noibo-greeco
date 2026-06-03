<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\DTOs\UserFilterDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Users')]
final class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $role = '';

    #[Url(as: 'sort', except: 'created_at')]
    public string $sortField = 'created_at';

    #[Url(as: 'direction', except: 'desc')]
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    private UserService $users;

    public function boot(UserService $users): void
    {
        $this->users = $users;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', User::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['name', 'email', 'created_at'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[On('users:refresh')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.users.user-index', [
            'users' => $this->users->paginate(UserFilterDTO::fromArray([
                'search' => $this->search,
                'role' => $this->role,
                'sort_field' => $this->sortField,
                'sort_direction' => $this->sortDirection,
                'per_page' => $this->perPage,
            ])),
            'roles' => $this->roleOptions(),
        ]);
    }

    /**
     * @return Collection<int, Role>
     */
    private function roleOptions(): Collection
    {
        return $this->users->roleOptions();
    }
}
