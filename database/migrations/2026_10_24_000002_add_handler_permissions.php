<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [PermissionEnum::HandlerView, PermissionEnum::HandlerCreate, PermissionEnum::HandlerUpdate, PermissionEnum::HandlerDelete];
        $admin = Role::where('name', RoleEnum::SuperAdmin->value)->where('guard_name', 'web')->first();
        foreach ($permissions as $permission) {
            $record = Permission::findOrCreate($permission->value, 'web');
            $admin?->givePermissionTo($record);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where('guard_name', 'web')->whereIn('name', ['handlers.view', 'handlers.create', 'handlers.edit', 'handlers.delete'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
