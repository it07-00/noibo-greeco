<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Marketing Department
        DB::table('departments')->updateOrInsert(
            ['code' => 'MKT'],
            [
                'name' => 'Phòng Marketing',
                'description' => 'Bộ phận Truyền thông & Marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $mktDeptId = DB::table('departments')->where('code', 'MKT')->value('id');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Create Marketing Role
        $role = Role::findOrCreate(RoleEnum::Marketing->value, 'web');
        $role->update([
            'description' => 'Bộ phận Marketing, truyền thông, soạn thảo bài viết và lên kế hoạch marketing',
            'department_id' => $mktDeptId,
        ]);

        // 3. Assign Permissions to Marketing Role
        $marketingPermissions = [
            PermissionEnum::DashboardView->value,
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value,
            PermissionEnum::MarketingPlanDelete->value,
            PermissionEnum::ScheduleView->value,
            PermissionEnum::ReportView->value,
            PermissionEnum::DocumentView->value,
            PermissionEnum::MailView->value,
            PermissionEnum::MailSend->value,
        ];

        foreach ($marketingPermissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $role->syncPermissions($marketingPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
