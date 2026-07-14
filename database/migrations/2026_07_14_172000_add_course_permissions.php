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

        foreach ([PermissionEnum::CourseView, PermissionEnum::CourseManage] as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $role = fn (RoleEnum $role): ?Role => Role::query()
            ->where('name', $role->value)
            ->where('guard_name', 'web')
            ->first();

        $role(RoleEnum::SuperAdmin)?->givePermissionTo(Permission::all());
        $role(RoleEnum::Director)?->givePermissionTo(PermissionEnum::CourseView->value);
        $role(RoleEnum::Sales)?->givePermissionTo([
            PermissionEnum::CourseView->value,
            PermissionEnum::CourseManage->value,
        ]);
        $role(RoleEnum::Consultant)?->givePermissionTo([
            PermissionEnum::CourseView->value,
            PermissionEnum::CourseManage->value,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
