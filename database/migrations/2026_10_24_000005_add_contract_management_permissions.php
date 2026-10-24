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
        $grants = [
            PermissionEnum::ContractViewAll->value => [RoleEnum::SuperAdmin, RoleEnum::Director, RoleEnum::Accountant, RoleEnum::IT],
            PermissionEnum::ContractExport->value => [RoleEnum::SuperAdmin, RoleEnum::Director, RoleEnum::Accountant, RoleEnum::Sales],
            PermissionEnum::ContractAssign->value => [RoleEnum::SuperAdmin, RoleEnum::Director, RoleEnum::Sales, RoleEnum::IT],
        ];
        foreach ($grants as $name => $roles) {
            $permission = Permission::findOrCreate($name, 'web');
            foreach ($roles as $role) {
                Role::where('name', $role->value)->where('guard_name', 'web')->first()?->givePermissionTo($permission);
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where('guard_name', 'web')->whereIn('name', ['contract.view-all', 'contract.export', 'contract.assign'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
