<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Models\Department;
use App\Services\UserService;
use App\Support\UserValidationRules;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
final class UserCreate extends Component
{
    public bool $isOpen = false;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    /**
     * @var list<string>
     */
    public array $roles = [];

    public $department_id = null;

    public string $dob = '';

    public string $address = '';

    private UserService $users;

    public function boot(UserService $users): void
    {
        $this->users = $users;
    }

    #[On('user-create:open')]
    public function open(): void
    {
        Gate::authorize('create', User::class);
        $this->resetValidation();
        $this->resetForm();
        $this->isOpen = true;
        $this->dispatch('user-create:show');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->dispatch('user-create:hide');
    }

    public function departmentChanged(): void
    {
        $availableRoles = $this->filteredRoleOptions()->pluck('name')->all();
        $this->roles = array_values(array_intersect($this->roles, $availableRoles));
    }

    public function save(): void
    {
        Gate::authorize('create', User::class);

        $validated = $this->validate(UserValidationRules::store());

        $this->users->create(UserDTO::fromArray($validated));

        $this->isOpen = false;
        $this->dispatch('user-create:hide');
        $this->dispatch('users:refresh');
        session()->flash('status', 'User created successfully.');
    }

    public function render(): View
    {
        return view('livewire.users.user-create', [
            'roleOptions' => $this->filteredRoleOptions(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->roles = [];
        $this->department_id = null;
        $this->dob = '';
        $this->address = '';
    }

    private function filteredRoleOptions(): Collection
    {
        $roles = $this->users->roleOptions();

        if (! $this->department_id) {
            return $roles;
        }

        return $roles->filter(fn ($role): bool => $role->department_id === null || (int) $role->department_id === (int) $this->department_id);
    }
}
