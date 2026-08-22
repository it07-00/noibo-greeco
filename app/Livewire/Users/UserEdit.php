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
final class UserEdit extends Component
{
    public bool $isOpen = false;

    public ?int $userId = null;

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

    #[On('user-edit:open')]
    public function open(int $userId): void
    {
        $user = $this->users->find($userId);
        Gate::authorize('update', $user);

        $this->resetValidation();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = (string) ($user->email ?? '');
        $this->password = '';
        $this->roles = $user->roles->pluck('name')->values()->all();
        $this->department_id = $user->department_id;
        $this->dob = $user->dob ? $user->dob->format('Y-m-d') : '';
        $this->address = $user->address ?? '';
        $this->isOpen = true;
        $this->dispatch('user-edit:show');
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->dispatch('user-edit:hide');
    }

    public function departmentChanged(): void
    {
        $availableRoles = $this->filteredRoleOptions()->pluck('name')->all();
        $this->roles = array_values(array_intersect($this->roles, $availableRoles));
    }

    public function save(): void
    {
        abort_if($this->userId === null, 404);

        $user = $this->users->find($this->userId);
        Gate::authorize('update', $user);

        $validated = $this->validate(UserValidationRules::update($user));

        $this->users->update($user, UserDTO::fromArray($validated));

        $this->isOpen = false;
        $this->dispatch('user-edit:hide');
        $this->dispatch('users:refresh');
        session()->flash('status', 'User updated successfully.');
    }

    public function render(): View
    {
        return view('livewire.users.user-edit', [
            'roleOptions' => $this->filteredRoleOptions(),
            'departments' => Department::orderBy('name')->get(),
        ]);
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
