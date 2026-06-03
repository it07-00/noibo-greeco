<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\RoleDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RolePermissionService
{
    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return Role::query()
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('id')
            ->get();
    }

    public function createRole(RoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto): Role {
            return Role::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'guard_name' => 'web',
            ]);
        });
    }

    public function updateRole(Role $role, RoleDTO $dto): Role
    {
        return DB::transaction(function () use ($role, $dto): Role {
            $role->update([
                'name' => $dto->name,
                'description' => $dto->description,
            ]);
            return $role->refresh();
        });
    }

    public function deleteRole(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            // Prevent deleting Super Admin
            if ($role->name === \App\Enums\RoleEnum::SuperAdmin->value) {
                throw new \InvalidArgumentException('Không thể xóa vai trò Super Admin');
            }
            $role->delete();
        });
    }

    /**
     * @param array<int, string> $permissionNames
     */
    public function syncPermissions(Role $role, array $permissionNames): void
    {
        DB::transaction(function () use ($role, $permissionNames): void {
            // Super Admin should always have all permissions, protect it
            if ($role->name === \App\Enums\RoleEnum::SuperAdmin->value) {
                $role->syncPermissions(Permission::all());
                return;
            }

            $role->syncPermissions($permissionNames);
        });
    }
}
