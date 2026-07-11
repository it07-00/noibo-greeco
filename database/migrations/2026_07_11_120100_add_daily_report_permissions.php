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
            PermissionEnum::ReportView,
            PermissionEnum::ReportCreate,
            PermissionEnum::ReportUpdate,
            PermissionEnum::ReportDelete,
        ] as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $superAdmin = Role::query()->where('name', RoleEnum::SuperAdmin->value)->where('guard_name', 'web')->first();
        $director = Role::query()->where('name', RoleEnum::Director->value)->where('guard_name', 'web')->first();
        $it = Role::query()->where('name', RoleEnum::IT->value)->where('guard_name', 'web')->first();
        $sales = Role::query()->where('name', RoleEnum::Sales->value)->where('guard_name', 'web')->first();
        $consultant = Role::query()->where('name', RoleEnum::Consultant->value)->where('guard_name', 'web')->first();
        $accountant = Role::query()->where('name', RoleEnum::Accountant->value)->where('guard_name', 'web')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                PermissionEnum::ReportView->value,
                PermissionEnum::ReportCreate->value,
                PermissionEnum::ReportUpdate->value,
                PermissionEnum::ReportDelete->value,
            ]);
        }

        $director?->givePermissionTo([
            PermissionEnum::ReportView->value,
        ]);

        $it?->givePermissionTo([
            PermissionEnum::ReportView->value,
            PermissionEnum::ReportCreate->value,
            PermissionEnum::ReportUpdate->value,
        ]);

        $sales?->givePermissionTo([
            PermissionEnum::ReportView->value,
            PermissionEnum::ReportCreate->value,
            PermissionEnum::ReportUpdate->value,
        ]);

        $consultant?->givePermissionTo([
            PermissionEnum::ReportView->value,
            PermissionEnum::ReportCreate->value,
            PermissionEnum::ReportUpdate->value,
        ]);

        $accountant?->givePermissionTo([
            PermissionEnum::ReportView->value,
            PermissionEnum::ReportCreate->value,
            PermissionEnum::ReportUpdate->value,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
