<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\DailyReport;
use App\Models\DutySchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create target users for each role with predictable emails
        $users = [];

        $roleUsers = [
            RoleEnum::Director->value => [
                'name' => 'Nguyễn Giám Đốc',
                'email' => 'giamdoc@example.com',
            ],
            RoleEnum::IT->value => [
                'name' => 'Trần Kỹ Thuật (IT)',
                'email' => 'it@example.com',
            ],
            RoleEnum::Sales->value => [
                'name' => 'Lê Kinh Doanh',
                'email' => 'sales@example.com',
            ],
            RoleEnum::Accountant->value => [
                'name' => 'Phạm Kế Toán',
                'email' => 'ketoan@example.com',
            ],
            RoleEnum::Consultant->value => [
                'name' => 'Hoàng Tư Vấn',
                'email' => 'tuvan@example.com',
            ],
        ];

        foreach ($roleUsers as $roleName => $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Sync role to user
            $user->syncRoles([$roleName]);
            $users[] = $user;
        }

        // Fetch super admin if exists
        $superAdmin = User::query()->where('email', 'superadmin@example.com')->first();
        if ($superAdmin) {
            $users[] = $superAdmin;
        }

        // 2. Generate daily reports for the past 7 days
        // Staff users can create reports. Directors do not need to.
        $staffUsers = array_filter($users, function (User $u) {
            return !$u->hasRole(RoleEnum::Director->value);
        });

        foreach ($staffUsers as $user) {
            for ($i = 0; $i < 7; $i++) {
                $date = now()->subDays($i)->toDateString();

                // Create daily report
                DailyReport::factory()->create([
                    'user_id' => $user->id,
                    'report_date' => $date,
                ]);
            }
        }

        // 3. Generate duty schedules for the past 5 days and next 5 days
        foreach ($users as $user) {
            // Create 3-4 schedules for each user at random dates
            for ($i = 0; $i < 4; $i++) {
                $start = now()->addDays(rand(-5, 5))->setHour(rand(8, 16))->setMinute(0)->setSecond(0);
                $end = (clone $start)->addHour(rand(1, 3));

                DutySchedule::factory()->create([
                    'created_by' => $user->id,
                    'start_at' => $start,
                    'end_at' => $end,
                ]);
            }
        }
    }
}
