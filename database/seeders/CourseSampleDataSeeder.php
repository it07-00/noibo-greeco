<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CustomerType;
use App\Models\Course;
use App\Models\Customer;
use Illuminate\Database\Seeder;

final class CourseSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $students = collect([
            [
                'name' => 'Nguyễn Minh Anh',
                'email' => 'minhanh.hocvien@example.com',
                'phone' => '0901000001',
                'province' => 'TP. Hồ Chí Minh',
                'industry' => 'Chuyên viên phát triển bền vững',
            ],
            [
                'name' => 'Trần Quốc Bảo',
                'email' => 'quocbao.hocvien@example.com',
                'phone' => '0901000002',
                'province' => 'Hà Nội',
                'industry' => 'Kỹ sư môi trường',
            ],
            [
                'name' => 'Lê Thảo Chi',
                'email' => 'thaochi.hocvien@example.com',
                'phone' => '0901000003',
                'province' => 'Đà Nẵng',
                'industry' => 'Quản lý chất lượng',
            ],
            [
                'name' => 'Phạm Hoàng Duy',
                'email' => 'hoangduy.hocvien@example.com',
                'phone' => '0901000004',
                'province' => 'Bình Dương',
                'industry' => 'Cán bộ an toàn lao động',
            ],
            [
                'name' => 'Võ Ngọc Hà',
                'email' => 'ngocha.hocvien@example.com',
                'phone' => '0901000005',
                'province' => 'Đồng Nai',
                'industry' => 'Tư vấn ESG',
            ],
        ])->map(fn (array $student): Customer => Customer::query()->updateOrCreate(
            ['email' => $student['email']],
            [...$student, 'type' => CustomerType::Individual],
        ));

        $courses = collect([
            [
                'code' => 'ESG-CB-2026',
                'name' => 'ESG và phát triển bền vững căn bản',
                'starts_at' => now()->addDays(10)->toDateString(),
                'ends_at' => now()->addDays(12)->toDateString(),
                'location' => 'Viện GREECO - TP. Hồ Chí Minh',
                'description' => 'Kiến thức nền tảng về ESG, khung báo cáo và lộ trình triển khai tại doanh nghiệp.',
            ],
            [
                'code' => 'KNK-CARBON-2026',
                'name' => 'Kiểm kê khí nhà kính và quản lý carbon',
                'starts_at' => now()->addDays(25)->toDateString(),
                'ends_at' => now()->addDays(27)->toDateString(),
                'location' => 'Học trực tuyến qua Zoom',
                'description' => 'Thực hành thu thập dữ liệu, tính toán phát thải và lập báo cáo kiểm kê khí nhà kính.',
            ],
            [
                'code' => 'HSE-ATLD-2026',
                'name' => 'An toàn, sức khỏe và môi trường (HSE)',
                'starts_at' => now()->subDays(20)->toDateString(),
                'ends_at' => now()->subDays(18)->toDateString(),
                'location' => 'Trung tâm đào tạo GREECO - Hà Nội',
                'description' => 'Nhận diện mối nguy, đánh giá rủi ro và xây dựng hệ thống quản lý HSE.',
            ],
        ])->map(fn (array $course): Course => Course::query()->updateOrCreate(
            ['code' => $course['code']],
            $course,
        ));

        $courses[0]->students()->sync([
            $students[0]->id,
            $students[1]->id,
            $students[2]->id,
            $students[4]->id,
        ]);
        $courses[1]->students()->sync([
            $students[0]->id,
            $students[2]->id,
            $students[4]->id,
        ]);
        $courses[2]->students()->sync([
            $students[1]->id,
            $students[3]->id,
        ]);
    }
}
