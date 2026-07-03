<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insert default departments
        $departments = [
            ['name' => 'Ban giám đốc', 'code' => 'BGĐ', 'description' => 'Ban Giám đốc công ty'],
            ['name' => 'Hành chính Nhân sự', 'code' => 'HCNS', 'description' => 'Bộ phận Hành chính & Quản trị Nhân sự'],
            ['name' => 'Tài chính Kế toán', 'code' => 'TCKT', 'description' => 'Bộ phận Quản lý Tài chính & Kế toán'],
            ['name' => 'Công nghệ thông tin', 'code' => 'IT', 'description' => 'Bộ phận Công nghệ thông tin & Kỹ thuật'],
            ['name' => 'Kinh doanh', 'code' => 'KD', 'description' => 'Bộ phận Kinh doanh & Phát triển thị trường'],
            ['name' => 'Tư vấn & CSKH', 'code' => 'TV', 'description' => 'Bộ phận Tư vấn & Chăm sóc khách hàng'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['code' => $dept['code']],
                [
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // 2. Map default roles to departments
        $deptMap = [
            'Super Admin' => 'IT',
            'Giám đốc' => 'BGĐ',
            'IT' => 'IT',
            'Kinh doanh' => 'KD',
            'Tư vấn' => 'TV',
            'Kế toán' => 'TCKT',
        ];

        foreach ($deptMap as $roleName => $deptCode) {
            $deptId = DB::table('departments')->where('code', $deptCode)->value('id');
            if ($deptId) {
                DB::table('roles')
                    ->where('name', $roleName)
                    ->update(['department_id' => $deptId]);
            }
        }

        // 3. Map existing default users to departments
        $userMap = [
            'superadmin' => 'IT',
            'giamdoc' => 'BGĐ',
            'it' => 'IT',
            'sales' => 'KD',
            'ketoan' => 'TCKT',
            'tuvan' => 'TV',
        ];

        foreach ($userMap as $username => $deptCode) {
            $deptId = DB::table('departments')->where('code', $deptCode)->value('id');
            if ($deptId) {
                DB::table('users')
                    ->where('username', $username)
                    ->update(['department_id' => $deptId]);
            }
        }
    }

    public function down(): void
    {
        // Leave down empty to prevent destructive data loss in production rollback.
    }
};
