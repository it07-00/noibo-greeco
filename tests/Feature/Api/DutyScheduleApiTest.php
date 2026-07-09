<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\DutySchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DutyScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_all_public_duty_schedules_in_range(): void
    {
        config()->set('services.noibo.api_token', 'test-token');

        $creator = User::factory()->create();

        $publicSchedule = DutySchedule::create([
            'title' => 'Public site visit',
            'description' => 'Visible to integration',
            'location' => 'Factory',
            'start_at' => '2026-07-10 08:30:00',
            'end_at' => null,
            'label_color' => 'primary',
            'is_private' => false,
            'created_by' => $creator->id,
        ]);

        DutySchedule::create([
            'title' => 'Private appointment',
            'start_at' => '2026-07-10 09:00:00',
            'label_color' => 'danger',
            'is_private' => true,
            'created_by' => $creator->id,
        ]);

        $response = $this->getJson('/api/duty-schedules?start=2026-07-01&end=2026-07-31&token=test-token');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $publicSchedule->id)
            ->assertJsonPath('data.0.title', 'Public site visit');

        $this->assertCount(1, $response->json('data'));
    }
    public function test_it_exports_multi_day_duty_schedules_when_querying_a_covered_later_day(): void
    {
        config()->set('services.noibo.api_token', 'test-token');

        $creator = User::factory()->create();

        $schedule = DutySchedule::create([
            'title' => 'Two day course',
            'start_at' => '2026-07-22 07:01:00',
            'end_at' => '2026-07-23 17:25:00',
            'label_color' => 'warning',
            'is_private' => false,
            'created_by' => $creator->id,
        ]);

        $response = $this->getJson('/api/duty-schedules?start=2026-07-23&end=2026-07-23&token=test-token');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $schedule->id)
            ->assertJsonPath('data.0.start_date', '2026-07-22')
            ->assertJsonPath('data.0.end_date', '2026-07-23')
            ->assertJsonPath('data.0.start_time', '07:01:00')
            ->assertJsonPath('data.0.end_time', '17:25:00');
    }
}
