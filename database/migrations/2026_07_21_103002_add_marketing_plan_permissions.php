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

        $permissions = [
            PermissionEnum::MarketingPlanView,
            PermissionEnum::MarketingPlanCreate,
            PermissionEnum::MarketingPlanUpdate,
            PermissionEnum::MarketingPlanDelete,
            PermissionEnum::MarketingPlanApprove,
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $role = fn (RoleEnum $role): ?Role => Role::query()
            ->where('name', $role->value)
            ->where('guard_name', 'web')
            ->first();

        $role(RoleEnum::SuperAdmin)?->givePermissionTo(Permission::all());

        $role(RoleEnum::Director)?->givePermissionTo([
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value,
            PermissionEnum::MarketingPlanDelete->value,
            PermissionEnum::MarketingPlanApprove->value,
        ]);

        $role(RoleEnum::Sales)?->givePermissionTo([
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value,
            PermissionEnum::MarketingPlanDelete->value,
        ]);

        $role(RoleEnum::Consultant)?->givePermissionTo([
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
