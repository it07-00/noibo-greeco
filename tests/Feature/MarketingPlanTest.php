<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTOs\MarketingPlanDTO;
use App\Enums\MarketingCategory;
use App\Enums\MarketingPlanStatus;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\MarketingPlan;
use App\Models\User;
use App\Services\MarketingPlanService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class MarketingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_plans_table_can_store_records(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $plan = MarketingPlan::create([
            'title' => 'Bài viết ra mắt sản phẩm mới',
            'category' => MarketingCategory::Website,
            'content' => '<p>Nội dung chi tiết bài viết <strong>giải pháp xanh</strong>...</p>',
            'scheduled_at' => now()->addDays(3),
            'status' => MarketingPlanStatus::Pending,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('marketing_plans', [
            'id' => $plan->id,
            'title' => 'Bài viết ra mắt sản phẩm mới',
            'category' => 'website',
            'status' => 'pending_approval',
            'created_by' => $user->id,
        ]);
    }

    public function test_authorized_user_can_access_marketing_plans_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::MarketingPlanView->value);

        $this->actingAs($user)
            ->get(route('marketing-plans.index'))
            ->assertOk()
            ->assertSee('Kế hoạch Marketing & Nội dung');
    }

    public function test_unauthorized_user_cannot_access_marketing_plans_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('marketing-plans.index'))
            ->assertStatus(403);
    }

    public function test_marketing_plan_service_handles_crud_and_status_transitions(): void
    {
        Storage::fake('public');
        $this->seed(PermissionSeeder::class);

        $creator = User::factory()->create();
        $approver = User::factory()->create();
        $approver->assignRole(RoleEnum::Director->value);

        $service = app(MarketingPlanService::class);

        // 1. Create with image
        $imageFile = UploadedFile::fake()->image('banner.jpg', 800, 600);
        $dto = MarketingPlanDTO::fromArray([
            'title' => 'Chiến dịch Môi Trường Xanh',
            'category' => 'press',
            'content' => '<h1>Thông cáo báo chí</h1><p>Nội dung kịch bản truyền thông tuần tới</p>',
            'scheduled_at' => '2026-08-01 10:00:00',
            'status' => 'draft',
            'created_by' => $creator->id,
        ]);

        $plan = $service->create($dto, [$imageFile]);

        $this->assertDatabaseHas('marketing_plans', [
            'id' => $plan->id,
            'title' => 'Chiến dịch Môi Trường Xanh',
            'category' => 'press',
            'status' => 'draft',
        ]);

        $this->assertCount(1, $plan->images);
        Storage::disk('public')->assertExists($plan->images->first()->file_path);

        // 2. Submit for approval
        $service->submitForApproval($plan);
        $this->assertSame(MarketingPlanStatus::Pending, $plan->fresh()->status);

        // 3. Approve by manager
        $this->actingAs($approver);
        $approvedPlan = $service->approve($plan);
        $this->assertSame(MarketingPlanStatus::Approved, $approvedPlan->status);
        $this->assertSame($approver->id, $approvedPlan->approved_by);

        // 4. Reject
        $rejectedPlan = $service->reject($plan, 'Cần sửa lại hình ảnh đúng chuẩn logo');
        $this->assertSame(MarketingPlanStatus::Rejected, $rejectedPlan->status);
        $this->assertSame('Cần sửa lại hình ảnh đúng chuẩn logo', $rejectedPlan->rejection_reason);

        // 5. Delete
        $service->delete($plan);
        $this->assertSoftDeleted('marketing_plans', ['id' => $plan->id]);
    }

    public function test_livewire_component_creates_and_approves_marketing_plan(): void
    {
        $this->seed(PermissionSeeder::class);

        $creator = User::factory()->create();
        $creator->givePermissionTo(
            PermissionEnum::MarketingPlanView->value,
            PermissionEnum::MarketingPlanCreate->value,
            PermissionEnum::MarketingPlanUpdate->value
        );

        $approver = User::factory()->create();
        $approver->assignRole(RoleEnum::Director->value);

        // Creator submits plan
        $this->actingAs($creator);

        Livewire::test(\App\Livewire\Marketing\MarketingPlanIndex::class)
            ->set('title', 'Bài đăng truyền thông nội bộ')
            ->set('category', 'internal')
            ->set('content', '<p>Nội dung quảng bá dịch vụ <strong>chất lượng cao</strong></p>')
            ->set('scheduled_at', '2026-08-15T09:00')
            ->set('status', 'pending_approval')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('marketing:saved');

        $plan = MarketingPlan::where('title', 'Bài đăng truyền thông nội bộ')->first();
        $this->assertNotNull($plan);
        $this->assertSame(MarketingPlanStatus::Pending, $plan->status);
        $this->assertSame(MarketingCategory::Internal, $plan->category);

        // Approver approves plan
        $this->actingAs($approver);

        Livewire::test(\App\Livewire\Marketing\MarketingPlanIndex::class)
            ->call('approvePlan', $plan->id);

        $this->assertSame(MarketingPlanStatus::Approved, $plan->fresh()->status);
    }
}
