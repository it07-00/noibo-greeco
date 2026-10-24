<?php

declare(strict_types=1);

namespace App\Livewire\RolesPermissions;

use App\DTOs\RoleDTO;
use App\Enums\RoleEnum;
use App\Models\Department;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Quản lý Vai trò & Quyền')]
final class RolesPermissionsIndex extends Component
{
    public int $activeRoleId = 0;

    public string $newRoleName = '';

    public string $newRoleDescription = '';

    public $newRoleDepartmentId = null;

    public int $editingRoleId = 0;

    public string $editRoleName = '';

    public string $editRoleDescription = '';

    public $editRoleDepartmentId = null;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public string $permissionSearch = '';

    protected array $rules = [
        'newRoleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
        'newRoleDescription' => ['nullable', 'string', 'max:500'],
        'newRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        'editRoleName' => ['required', 'string', 'max:255'],
        'editRoleDescription' => ['nullable', 'string', 'max:500'],
        'editRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
    ];

    public function mount(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $firstRole = Role::query()->orderBy('id')->first();
        if ($firstRole) {
            $this->activeRoleId = (int) $firstRole->id;
        }
    }

    public function selectRole(int $roleId): void
    {
        Gate::authorize('manage', Role::class);
        $this->activeRoleId = $roleId;
    }

    public function togglePermission(RolePermissionService $service, string $permissionName): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($this->activeRoleId);

        if ($role->name === RoleEnum::SuperAdmin->value) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chỉnh sửa!',
                'text' => 'Vai trò Super Admin luôn có tất cả các quyền hạn.',
            ]);

            return;
        }

        $service->togglePermission($role, $permissionName);

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Cập nhật thành công!',
            'text' => 'Đã cập nhật quyền '.$permissionName.' cho vai trò '.$role->name,
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
    }

    public function toggleGroupPermissions(RolePermissionService $service, array $permissionKeys, bool $enable): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($this->activeRoleId);

        if ($role->name === RoleEnum::SuperAdmin->value) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chỉnh sửa!',
                'text' => 'Vai trò Super Admin luôn có tất cả các quyền hạn.',
            ]);

            return;
        }

        $activePerms = $role->permissions->pluck('name')->toArray();

        foreach ($permissionKeys as $permKey) {
            $hasPerm = in_array($permKey, $activePerms, true);
            if ($enable && ! $hasPerm) {
                $service->togglePermission($role, $permKey);
            } elseif (! $enable && $hasPerm) {
                $service->togglePermission($role, $permKey);
            }
        }

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Cập nhật thành công!',
            'text' => ($enable ? 'Đã cấp tất cả quyền trong nhóm' : 'Đã thu hồi tất cả quyền trong nhóm').' cho vai trò '.$role->name,
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
    }

    public function createRole(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $this->validate([
            'newRoleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'newRoleDescription' => ['nullable', 'string', 'max:500'],
            'newRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $dto = RoleDTO::fromArray([
            'name' => $this->newRoleName,
            'description' => $this->newRoleDescription,
            'department_id' => $this->newRoleDepartmentId,
        ]);

        $role = $service->createRole($dto);

        $this->activeRoleId = (int) $role->id;
        $this->newRoleName = '';
        $this->newRoleDescription = '';
        $this->newRoleDepartmentId = null;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Tạo thành công!',
            'text' => 'Đã thêm vai trò mới '.$role->name,
        ]);

        $this->dispatch('role-create:hide');
    }

    public function deleteRole(RolePermissionService $service, int $roleId): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($roleId);

        if (RoleEnum::isSystemRole($role->name)) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể xóa!',
                'text' => 'Vai trò hệ thống không thể bị xóa.',
            ]);

            return;
        }

        $service->deleteRole($role);

        $firstRole = Role::query()->orderBy('id')->first();
        $this->activeRoleId = $firstRole ? (int) $firstRole->id : 0;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Đã xóa!',
            'text' => 'Đã xóa vai trò '.$role->name.' thành công.',
        ]);
    }

    public function openEditModal(int $roleId): void
    {
        Gate::authorize('manage', Role::class);

        $role = Role::findOrFail($roleId);

        $this->editingRoleId = $roleId;
        $this->editRoleName = $role->name;
        $this->editRoleDescription = $role->description ?? '';
        $this->editRoleDepartmentId = $role->department_id;
        $this->resetValidation(['editRoleName', 'editRoleDescription', 'editRoleDepartmentId']);

        $this->dispatch('role-edit:show');
    }

    public function updateRole(RolePermissionService $service): void
    {
        Gate::authorize('manage', Role::class);

        $this->validate([
            'editRoleName' => ['required', 'string', 'max:255', 'unique:roles,name,'.$this->editingRoleId],
            'editRoleDescription' => ['nullable', 'string', 'max:500'],
            'editRoleDepartmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $role = Role::findOrFail($this->editingRoleId);

        if (RoleEnum::isSystemRole($role->name) && $role->name !== $this->editRoleName) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Không thể chỉnh sửa!',
                'text' => 'Vai trò hệ thống không thể đổi tên.',
            ]);

            return;
        }

        $dto = RoleDTO::fromArray([
            'name' => $this->editRoleName,
            'description' => $this->editRoleDescription,
            'department_id' => $this->editRoleDepartmentId,
        ]);

        $service->updateRole($role, $dto);

        $this->dispatch('role-edit:hide');

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Cập nhật thành công!',
            'text' => 'Đã cập nhật thông tin vai trò.',
            'toast' => true,
            'position' => 'top-end',
            'timer' => 3000,
        ]);
    }

    public function render(RolePermissionService $service): View
    {
        $roles = $service->getRoles();
        $activeRole = $roles->firstWhere('id', $this->activeRoleId) ?? $roles->first();

        $permissionsGrouped = $service->permissionGroups();

        $activeRolePermissions = $activeRole ? $activeRole->permissions->pluck('name')->toArray() : [];
        $availableDepartments = Department::orderBy('name')->get();

        $totalPermissionsCount = 0;
        foreach ($permissionsGrouped as $group) {
            $totalPermissionsCount += count($group);
        }

        $activeRolePermissionCount = ($activeRole && $activeRole->name === RoleEnum::SuperAdmin->value)
            ? $totalPermissionsCount
            : count($activeRolePermissions);

        // Filter permissions if search term is active
        if (trim($this->permissionSearch) !== '') {
            $query = mb_strtolower(trim($this->permissionSearch));
            $filteredGroups = [];

            foreach ($permissionsGrouped as $groupName => $permissions) {
                $groupNameMatch = mb_strpos(mb_strtolower($groupName), $query) !== false;
                $matchingPermissions = [];

                foreach ($permissions as $permValue => $permLabel) {
                    if ($groupNameMatch || mb_strpos(mb_strtolower($permLabel), $query) !== false || mb_strpos(mb_strtolower($permValue), $query) !== false) {
                        $matchingPermissions[$permValue] = $permLabel;
                    }
                }

                if (! empty($matchingPermissions)) {
                    $filteredGroups[$groupName] = $matchingPermissions;
                }
            }

            $permissionsGrouped = $filteredGroups;
        }

        return view('livewire.roles-permissions.roles-permissions-index', [
            'roles' => $roles,
            'activeRole' => $activeRole,
            'permissionsGrouped' => $permissionsGrouped,
            'activeRolePermissions' => $activeRolePermissions,
            'availableDepartments' => $availableDepartments,
            'totalPermissionsCount' => $totalPermissionsCount,
            'activeRolePermissionCount' => $activeRolePermissionCount,
        ]);
    }
}
