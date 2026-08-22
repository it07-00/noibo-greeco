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

        $role = Role::findOrCreate(RoleEnum::Marketing->value, 'web');

        $staffPermissions = [
            PermissionEnum::DashboardView->value,
            PermissionEnum::ScheduleView->value,
            PermissionEnum::ScheduleCreate->value,
            PermissionEnum::ScheduleUpdate->value,
            PermissionEnum::ScheduleDelete->value,
            PermissionEnum::ReportView->value,
            PermissionEnum::ReportCreate->value,
            PermissionEnum::ReportUpdate->value,
            PermissionEnum::DocumentView->value,
            PermissionEnum::MailView->value,
            PermissionEnum::MailSend->value,
            PermissionEnum::MailUpdate->value,
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value,
            PermissionEnum::MarketingPlanDelete->value,
        ];

        foreach ($staffPermissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $role->givePermissionTo($staffPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
