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

        foreach ([
            PermissionEnum::CommissionView,
            PermissionEnum::CommissionCreate,
            PermissionEnum::CommissionUpdate,
            PermissionEnum::CommissionDelete,
            PermissionEnum::CommissionApprove,
            PermissionEnum::CommissionPay,
        ] as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $superAdmin = Role::query()->where('name', RoleEnum::SuperAdmin->value)->where('guard_name', 'web')->first();
        $director = Role::query()->where('name', RoleEnum::Director->value)->where('guard_name', 'web')->first();
        $it = Role::query()->where('name', RoleEnum::IT->value)->where('guard_name', 'web')->first();
        $sales = Role::query()->where('name', RoleEnum::Sales->value)->where('guard_name', 'web')->first();
        $accountant = Role::query()->where('name', RoleEnum::Accountant->value)->where('guard_name', 'web')->first();

        $superAdmin?->givePermissionTo(Permission::all());

        $director?->givePermissionTo([
            PermissionEnum::CommissionView->value,
        ]);

        $it?->givePermissionTo([
            PermissionEnum::CommissionView->value,
        ]);

        $sales?->givePermissionTo([
            PermissionEnum::CommissionView->value,
            PermissionEnum::CommissionCreate->value,
            PermissionEnum::CommissionUpdate->value,
            PermissionEnum::CommissionDelete->value,
        ]);

        $accountant?->givePermissionTo([
            PermissionEnum::CommissionView->value,
            PermissionEnum::CommissionApprove->value,
            PermissionEnum::CommissionPay->value,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
