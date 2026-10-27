<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Livewire\DailyReports\DailyReportIndex;
use App\Livewire\Users\UserPermissions;
use App\Models\DailyReport;
use App\Models\User;
use App\Services\DailyReportService;
use App\Services\DashboardService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class DailyReportViewAllPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_direct_permission_can_be_granted_and_revoked_for_a_report_creator(): void
    {
        $manager = User::factory()->create();
        $manager->givePermissionTo(PermissionEnum::RoleManage->value);
        $staff = User::factory()->create();
        $staff->assignRole(RoleEnum::IT->value);
        $colleague = User::factory()->create();
        $colleague->assignRole(RoleEnum::IT->value);
        $report = DailyReport::factory()->create(['user_id' => $colleague->id, 'report_date' => now()->toDateString()]);

        $this->actingAs($manager);
        Livewire::test(UserPermissions::class)
            ->call('open', $staff->id)
            ->assertSee('Xem tất cả báo cáo ngày')
            ->set('permissions', [PermissionEnum::ReportViewAll->value])
            ->call('save')
            ->assertHasNoErrors();

        $staff->refresh();
        $this->assertTrue($staff->hasDirectPermission(PermissionEnum::ReportView->value));
        $this->assertTrue($staff->can('create', DailyReport::class));
        $this->assertTrue($staff->can('view', $report));
        $this->assertFalse($colleague->can('viewAll', DailyReport::class));

        $this->actingAs($staff);
        $component = Livewire::test(DailyReportIndex::class)
            ->assertSet('viewMode', 'all')
            ->assertViewHas('reports', fn ($reports) => $reports->contains('id', $report->id))
            ->call('showDayReports', now()->toDateString())
            ->assertSet('dayReports.0.id', $report->id)
            ->call('openDetailModal', $report->id)
            ->assertSet('viewingId', $report->id);
        $events = $component->instance()->getEvents(now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString(), app(DailyReportService::class));
        $this->assertSame([$report->id], array_column($events, 'id'));
        $this->assertSame($report->created_at->format('H:i d/m/Y'), $events[0]['created_at_formatted']);
        $component->set('viewType', 'table')->assertSee('Nộp lúc: '.$report->created_at->format('H:i d/m/Y'));
        $this->assertCount(1, app(DashboardService::class)->recentReports());
        $this->assertFalse($staff->can('update', $report));
        $this->assertFalse($staff->can('delete', $report));

        $this->actingAs($manager);
        Livewire::test(UserPermissions::class)
            ->call('open', $staff->id)
            ->set('permissions', [])
            ->call('save')
            ->assertHasNoErrors();
        $this->actingAs($staff->fresh());
        $this->assertFalse(auth()->user()->can('view', $report));
        Livewire::test(DailyReportIndex::class)->call('openDetailModal', $report->id)->assertForbidden();
    }

    public function test_user_without_view_all_cannot_expand_scope_by_changing_livewire_properties(): void
    {
        $staff = User::factory()->create();
        $staff->givePermissionTo(PermissionEnum::ReportView->value);
        $own = DailyReport::factory()->create(['user_id' => $staff->id, 'report_date' => now()->toDateString()]);
        $other = DailyReport::factory()->create(['report_date' => now()->toDateString()]);
        $this->actingAs($staff);

        $component = Livewire::test(DailyReportIndex::class)
            ->assertSet('viewMode', 'mine')
            ->set('viewMode', 'all')
            ->set('filterUserId', $other->user_id)
            ->assertViewHas('reports', fn ($reports) => $reports->modelKeys() === [$own->id])
            ->call('showDayReports', now()->toDateString())
            ->assertSet('dayReports.0.id', $own->id);
        $events = $component->instance()->getEvents(now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString(), app(DailyReportService::class));
        $this->assertSame([$own->id], array_column($events, 'id'));
        Livewire::test(DailyReportIndex::class)->set('viewingId', $other->id)->assertForbidden();
    }

    public function test_migration_preserves_existing_viewers_without_granting_all_staff_access(): void
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo(PermissionEnum::ReportView->value);
        $staff = User::factory()->create();
        $staff->assignRole(RoleEnum::IT->value);
        $director = User::factory()->create();
        $director->assignRole(RoleEnum::Director->value);

        $migration = require database_path('migrations/2026_10_27_000001_add_daily_report_view_all_permission.php');
        $migration->down();
        $migration->up();
        $migration->up();

        $this->assertTrue($viewer->fresh()->can('viewAll', DailyReport::class));
        $this->assertTrue($director->fresh()->can('viewAll', DailyReport::class));
        $this->assertFalse($staff->fresh()->can('viewAll', DailyReport::class));
        $this->assertSame(1, $viewer->fresh()->permissions()->where('name', PermissionEnum::ReportViewAll->value)->count());
    }
}
