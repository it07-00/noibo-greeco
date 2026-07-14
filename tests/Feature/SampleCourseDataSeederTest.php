<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CustomerType;
use App\Models\Course;
use App\Models\Customer;
use Database\Seeders\CourseSampleDataSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SampleCourseDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_sample_data_contains_courses_and_students_with_multiple_enrollments(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(CourseSampleDataSeeder::class);

        self::assertSame(3, Course::query()->count());
        self::assertSame(5, Customer::query()->where('type', CustomerType::Individual->value)->count());

        $student = Customer::query()->where('email', 'minhanh.hocvien@example.com')->firstOrFail();
        self::assertCount(2, $student->courses);

        $course = Course::query()->where('code', 'ESG-CB-2026')->firstOrFail();
        self::assertCount(4, $course->students);
    }
}
