<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission = Permission::findOrCreate(PermissionEnum::ReportViewAll->value, 'web');

        // Preserve access for existing users who could see all reports under the old rule.
        User::permission(PermissionEnum::ReportView->value)->chunkById(100, function ($users) use ($permission): void {
            foreach ($users as $user) {
                if (! $user->hasRole([RoleEnum::SuperAdmin->value, RoleEnum::Director->value])
                    && ! $user->can(PermissionEnum::ReportCreate->value)) {
                    $user->givePermissionTo($permission);
                }
            }
        });

        foreach ([RoleEnum::SuperAdmin, RoleEnum::Director] as $role) {
            Role::where('name', $role->value)->where('guard_name', 'web')->first()?->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where('name', 'report.view-all')->where('guard_name', 'web')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
