<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

final class UserPermissions extends Component
{
    #[Locked]
    public ?int $userId = null;

    /** @var list<string> */
    public array $permissions = [];

    public string $search = '';

    #[On('user-permissions:open')]
    public function open(int $userId): void
    {
        Gate::authorize('manage', Role::class);
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->permissions = $user->permissions->pluck('name')->all();
        $this->search = '';
        $this->resetValidation();
        $this->dispatch('user-permissions:show');
    }

    public function close(): void
    {
        $this->reset('userId', 'permissions', 'search');
        $this->resetValidation();
        $this->dispatch('user-permissions:hide');
    }

    public function save(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);
        $user = User::findOrFail($this->userId);
        abort_if($user->hasRole(RoleEnum::SuperAdmin->value), 403);

        $this->validate([
            'permissions' => ['array'],
            'permissions.*' => ['required', 'string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $service->syncUserPermissions($user, $this->permissions);
        $this->close();
        $this->dispatch('users:refresh');
        session()->flash('status', 'Đã cập nhật quyền riêng cho người dùng.');
    }

    public function render(RolePermissionService $service): View
    {
        $user = null;
        if ($this->userId !== null) {
            Gate::authorize('manage', Role::class);
            $user = User::with('roles.permissions')->findOrFail($this->userId);
        }

        $groups = $service->permissionGroups();

        if (trim($this->search) !== '') {
            $search = mb_strtolower(trim($this->search));
            foreach ($groups as $group => $permissions) {
                $groups[$group] = array_filter($permissions, fn ($label, $name) => str_contains(mb_strtolower($group.' '.$label.' '.$name), $search), ARRAY_FILTER_USE_BOTH);
            }
        }

        return view('livewire.users.user-permissions', [
            'selectedUser' => $user,
            'permissionGroups' => array_filter($groups),
            'inheritedPermissions' => $user?->getPermissionsViaRoles()->pluck('name')->all() ?? [],
            'isSuperAdmin' => $user?->hasRole(RoleEnum::SuperAdmin->value) ?? false,
        ]);
    }
}
