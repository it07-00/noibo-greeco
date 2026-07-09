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

        Permission::findOrCreate(PermissionEnum::SalesReportViewAll->value, 'web');

        $superAdmin = Role::query()->where('name', RoleEnum::SuperAdmin->value)->where('guard_name', 'web')->first();
        $superAdmin?->givePermissionTo(PermissionEnum::SalesReportViewAll->value);

        $director = Role::query()->where('name', RoleEnum::Director->value)->where('guard_name', 'web')->first();
        $director?->givePermissionTo(PermissionEnum::SalesReportViewAll->value);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Safe rollback
    }
};
