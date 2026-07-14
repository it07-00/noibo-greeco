<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContractType;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Models\Customer;
use App\Models\DailyReport;
use App\Models\DutySchedule;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                'username' => 'giamdoc',
                'email' => 'giamdoc@example.com',
                'dept_code' => 'BGĐ',
            ],
            RoleEnum::IT->value => [
                'name' => 'Trần Kỹ Thuật (IT)',
                'username' => 'it',
                'email' => 'it@example.com',
                'dept_code' => 'IT',
            ],
            RoleEnum::Sales->value => [
                'name' => 'Lê Kinh Doanh',
                'username' => 'sales',
                'email' => 'sales@example.com',
                'dept_code' => 'KD',
            ],
            RoleEnum::Accountant->value => [
                'name' => 'Phạm Kế Toán',
                'username' => 'ketoan',
                'email' => 'ketoan@example.com',
                'dept_code' => 'TCKT',
            ],
            RoleEnum::Consultant->value => [
                'name' => 'Hoàng Tư Vấn',
                'username' => 'tuvan',
                'email' => 'tuvan@example.com',
                'dept_code' => 'TV',
            ],
        ];

        foreach ($roleUsers as $roleName => $data) {
            $deptId = DB::table('departments')
                ->where('code', $data['dept_code'])
                ->value('id');

            $user = User::query()->firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'department_id' => $deptId,
                ]
            );

            $user->update(['department_id' => $deptId]);

            // Sync role to user
            $user->syncRoles([$roleName]);
            $users[] = $user;
        }

        // Fetch super admin if exists
        $superAdmin = User::query()->where('username', 'superadmin')->first();
        if ($superAdmin) {
            $users[] = $superAdmin;
        }

        // 2. Generate daily reports for the past 7 days
        // Staff users can create reports. Directors do not need to.
        $staffUsers = array_filter($users, function (User $u) {
            return ! $u->hasRole(RoleEnum::Director->value);
        });

        foreach ($staffUsers as $user) {
            for ($i = 0; $i < 7; $i++) {
                $date = now()->subDays($i)->toDateString();

                // Create daily report if not exists
                if (! DailyReport::query()->where('user_id', $user->id)->where('report_date', $date)->exists()) {
                    DailyReport::factory()->create([
                        'user_id' => $user->id,
                        'report_date' => $date,
                    ]);
                }
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

        // 4. Generate Sample Customers and Quotations
        $salesUser = User::query()->where('username', 'sales')->first();
        if ($salesUser) {
            $vinamilk = Customer::query()->firstOrCreate(
                ['tax_code' => '0300588569'],
                [
                    'name' => 'Công ty Cổ phần Sữa Việt Nam (Vinamilk)',
                    'province' => 'TP. Hồ Chí Minh',
                    'billing_address' => '10 Tân Trào, Phường Tân Phú, Quận 7, TP. Hồ Chí Minh',
                    'contact_name' => 'Nguyễn Anh Tuấn',
                    'email' => 'contact@vinamilk.com.vn',
                    'phone' => '02854155555',
                ]
            );

            $vingroup = Customer::query()->firstOrCreate(
                ['tax_code' => '0101245486'],
                [
                    'name' => 'Tập đoàn Vingroup - CTCP',
                    'province' => 'Hà Nội',
                    'billing_address' => 'Số 7 Đường Bằng Lăng 1, Phường Việt Hưng, Quận Long Biên, Hà Nội',
                    'contact_name' => 'Trần Thị Mai',
                    'email' => 'info@vingroup.net',
                    'phone' => '02439749999',
                ]
            );

            $fpt = Customer::query()->firstOrCreate(
                ['tax_code' => '0100277029'],
                [
                    'name' => 'Công ty Cổ phần FPT',
                    'province' => 'Hà Nội',
                    'billing_address' => 'Tòa nhà FPT, Phố Duy Tân, Phường Dịch Vọng Hậu, Quận Cầu Giấy, Hà Nội',
                    'contact_name' => 'Phạm Minh Nam',
                    'email' => 'support@fpt.com.vn',
                    'phone' => '02473007300',
                ]
            );

            // Vinamilk: Quotation for ESG consulting
            $q1 = Quotation::query()->create([
                'customer_id' => $vinamilk->id,
                'owner_id' => $salesUser->id,
                'contract_type' => ContractType::Consulting,
                'status' => QuotationStatus::FollowingUp,
                'issued_at' => now()->subDays(5),
                'valid_until' => now()->addDays(25),
                'original_amount' => 180000000,
                'total_amount' => 180000000,
                'customer_commission' => 10000000,
                'commission_tax' => 1000000,
                'contract_value' => 171000000,
                'working_situation' => 'Đang chờ phản hồi từ khách hàng.',
            ]);
            $q1->update([
                'quotation_number' => sprintf('BG-%s-%04d', now()->format('Y'), $q1->id),
            ]);
            $q1->services()->create([
                'service_type' => ServiceType::EsgConsulting,
                'description' => 'Đánh giá hiện trạng và xây dựng lộ trình ESG năm 2026',
                'quantity' => 1,
                'unit_price' => 120000000,
                'total_amount' => 120000000,
                'sort_order' => 0,
            ]);
            $q1->services()->create([
                'service_type' => ServiceType::CbamConsulting,
                'description' => 'Tư vấn lập báo cáo CBAM cho các sản phẩm xuất khẩu',
                'quantity' => 1,
                'unit_price' => 60000000,
                'total_amount' => 60000000,
                'sort_order' => 1,
            ]);

            // Vingroup: Quotation for Solar Project
            $q2 = Quotation::query()->create([
                'customer_id' => $vingroup->id,
                'owner_id' => $salesUser->id,
                'contract_type' => ContractType::Project,
                'status' => QuotationStatus::Won,
                'issued_at' => now()->subDays(10),
                'valid_until' => now()->addDays(20),
                'original_amount' => 750000000,
                'total_amount' => 750000000,
                'customer_commission' => 0,
                'commission_tax' => 0,
                'contract_value' => 750000000,
                'working_situation' => 'Khách hàng đã thống nhất phạm vi và đang chuẩn bị ký hợp đồng.',
            ]);
            $q2->update([
                'quotation_number' => sprintf('BG-%s-%04d', now()->format('Y'), $q2->id),
            ]);
            $q2->services()->create([
                'service_type' => ServiceType::SolarEnergyProject,
                'description' => 'Thiết kế và lắp đặt hệ thống điện mặt trời mái nhà 100kWp',
                'quantity' => 1,
                'unit_price' => 750000000,
                'total_amount' => 750000000,
                'sort_order' => 0,
            ]);

            // FPT: Quotation for training
            $q3 = Quotation::query()->create([
                'customer_id' => $fpt->id,
                'owner_id' => $salesUser->id,
                'contract_type' => ContractType::Training,
                'status' => QuotationStatus::Draft,
                'issued_at' => now(),
                'valid_until' => now()->addMonth(),
                'original_amount' => 45000000,
                'total_amount' => 45000000,
                'customer_commission' => 5000000,
                'commission_tax' => 500000,
                'contract_value' => 40500000,
                'working_situation' => 'Khách hàng đề xuất đàm phán giá.',
            ]);
            $q3->update([
                'quotation_number' => sprintf('BG-%s-%04d', now()->format('Y'), $q3->id),
            ]);
            $q3->services()->create([
                'service_type' => ServiceType::EsgSustainabilityTraining,
                'description' => 'Khóa đào tạo nhận thức về ESG và Phát triển bền vững cho Ban Quản trị',
                'quantity' => 1,
                'unit_price' => 45000000,
                'total_amount' => 45000000,
                'sort_order' => 0,
            ]);
        }

        $this->call(CourseSampleDataSeeder::class);
    }
}
