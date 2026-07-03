<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default departments
        $departments = [
            ['name' => 'Ban giám đốc', 'code' => 'BGĐ', 'description' => 'Ban Giám đốc công ty'],
            ['name' => 'Hành chính Nhân sự', 'code' => 'HCNS', 'description' => 'Bộ phận Hành chính & Quản trị Nhân sự'],
            ['name' => 'Tài chính Kế toán', 'code' => 'TCKT', 'description' => 'Bộ phận Quản lý Tài chính & Kế toán'],
            ['name' => 'Công nghệ thông tin', 'code' => 'IT', 'description' => 'Bộ phận Công nghệ thông tin & Kỹ thuật'],
            ['name' => 'Kinh doanh', 'code' => 'KD', 'description' => 'Bộ phận Kinh doanh & Phát triển thị trường'],
            ['name' => 'Tư vấn & CSKH', 'code' => 'TV', 'description' => 'Bộ phận Tư vấn & Chăm sóc khách hàng'],
        ];

        foreach ($departments as $dept) {
            \Illuminate\Support\Facades\DB::table('departments')->updateOrInsert(
                ['code' => $dept['code']],
                ['name' => $dept['name'], 'description' => $dept['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $deptBgd = \Illuminate\Support\Facades\DB::table('departments')->where('code', 'BGĐ')->value('id');
        $deptIt = \Illuminate\Support\Facades\DB::table('departments')->where('code', 'IT')->value('id');
        $deptKd = \Illuminate\Support\Facades\DB::table('departments')->where('code', 'KD')->value('id');
        $deptTv = \Illuminate\Support\Facades\DB::table('departments')->where('code', 'TV')->value('id');
        $deptTckt = \Illuminate\Support\Facades\DB::table('departments')->where('code', 'TCKT')->value('id');

        $superAdmin = Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');
        $superAdmin->update([
            'description' => 'Quyền quản trị tối cao, kiểm soát toàn bộ hệ thống',
            'department_id' => $deptIt,
        ]);

        $director = Role::findOrCreate(RoleEnum::Director->value, 'web');
        $director->update([
            'description' => 'Ban Giám đốc, xem báo cáo tổng quan và duyệt lịch trình',
            'department_id' => $deptBgd,
        ]);

        $it = Role::findOrCreate(RoleEnum::IT->value, 'web');
        $it->update([
            'description' => 'Phòng kỹ thuật, bảo trì hệ thống và hỗ trợ người dùng',
            'department_id' => $deptIt,
        ]);

        $sales = Role::findOrCreate(RoleEnum::Sales->value, 'web');
        $sales->update([
            'description' => 'Phòng kinh doanh, chăm sóc khách hàng và bán hàng',
            'department_id' => $deptKd,
        ]);

        $consultant = Role::findOrCreate(RoleEnum::Consultant->value, 'web');
        $consultant->update([
            'description' => 'Bộ phận tư vấn, hỗ trợ nghiệp vụ và hướng dẫn nghiệp vụ',
            'department_id' => $deptTv,
        ]);

        $accountant = Role::findOrCreate(RoleEnum::Accountant->value, 'web');
        $accountant->update([
            'description' => 'Bộ phận kế toán, quản lý tài chính và bảng lương',
            'department_id' => $deptTckt,
        ]);

        $superAdmin->syncPermissions(Permission::all());

        $directorPermissions = [
            PermissionEnum::DashboardView->value,
            PermissionEnum::ScheduleView->value,
            PermissionEnum::ScheduleCreate->value,
            PermissionEnum::ScheduleUpdate->value,
            PermissionEnum::ScheduleDelete->value,
            PermissionEnum::ScheduleViewPrivate->value,
            PermissionEnum::ReportView->value,
            PermissionEnum::DocumentView->value,
            PermissionEnum::DocumentManage->value,
            PermissionEnum::MailView->value,
            PermissionEnum::MailSend->value,
            PermissionEnum::MailUpdate->value,
        ];

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
        ];

        $director->syncPermissions($directorPermissions);
        $it->syncPermissions($staffPermissions);
        $it->givePermissionTo(PermissionEnum::DocumentManage->value);
        $sales->syncPermissions($staffPermissions);
        $consultant->syncPermissions($staffPermissions);
        $accountant->syncPermissions($staffPermissions);

        $itDeptId = \Illuminate\Support\Facades\DB::table('departments')
            ->where('code', 'IT')
            ->value('id');

        $user = User::query()->firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'department_id' => $itDeptId,
            ],
        );

        $user->update(['department_id' => $itDeptId]);
        $user->assignRole(RoleEnum::SuperAdmin->value);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
