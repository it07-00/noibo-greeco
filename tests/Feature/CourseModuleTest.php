<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CustomerType;
use App\Enums\RoleEnum;
use App\Livewire\Courses\CourseIndex;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class CourseModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_user_can_create_course_with_multiple_students(): void
    {
        $this->actingAs($this->salesUser());

        $firstStudent = Customer::query()->create([
            'name' => 'Học viên Một',
            'type' => CustomerType::Individual,
        ]);
        $secondStudent = Customer::query()->create([
            'name' => 'Học viên Hai',
            'type' => CustomerType::Individual,
        ]);

        Livewire::test(CourseIndex::class)
            ->set('code', 'esg-01')
            ->set('name', 'Khóa học ESG căn bản')
            ->set('startsAt', '2026-08-01')
            ->set('endsAt', '2026-08-03')
            ->set('selectedStudentIds', [$firstStudent->id, $secondStudent->id])
            ->call('save')
            ->assertHasNoErrors();

        $course = Course::query()->where('code', 'ESG-01')->firstOrFail();

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'customer_id' => $firstStudent->id,
        ]);
        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'customer_id' => $secondStudent->id,
        ]);
    }

    public function test_one_student_can_enroll_in_multiple_courses(): void
    {
        $this->actingAs($this->salesUser());

        $student = Customer::query()->create([
            'name' => 'Học viên Nhiều Khóa',
            'type' => CustomerType::Individual,
        ]);

        foreach (['Khóa ISO', 'Khóa HSE'] as $name) {
            Livewire::test(CourseIndex::class)
                ->set('name', $name)
                ->set('selectedStudentIds', [$student->id])
                ->call('save')
                ->assertHasNoErrors();
        }

        self::assertCount(2, $student->fresh()->courses);

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Khóa ISO')
            ->assertSee('Khóa HSE')
            ->assertSee('Học viên Nhiều Khóa');
    }

    public function test_organization_cannot_be_added_as_course_student(): void
    {
        $this->actingAs($this->salesUser());

        $organization = Customer::query()->create([
            'name' => 'Công ty Không Phải Học Viên',
            'type' => CustomerType::Organization,
        ]);

        Livewire::test(CourseIndex::class)
            ->set('name', 'Khóa học thử nghiệm')
            ->set('selectedStudentIds', [$organization->id])
            ->call('save')
            ->assertHasErrors(['selectedStudentIds.0']);

        $this->assertDatabaseMissing('courses', ['name' => 'Khóa học thử nghiệm']);
    }

    private function salesUser(): User
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Sales->value);

        return $user;
    }
}
