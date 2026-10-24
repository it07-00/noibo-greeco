<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Livewire\Users\UserPermissions;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

final class UserPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_manager_can_grant_persist_and_revoke_direct_permissions_without_changing_roles(): void
    {
        $manager = User::factory()->create();
        $manager->givePermissionTo(PermissionEnum::RoleManage->value);
        $role = Role::create(['name' => 'Individual permission test', 'guard_name' => 'web']);
        $role->givePermissionTo(PermissionEnum::DocumentView->value);
        $target = User::factory()->create();
        $target->assignRole($role);
        $colleague = User::factory()->create();
        $colleague->assignRole($role);
        $this->actingAs($manager);

        Livewire::test(UserPermissions::class)
            ->call('open', $target->id)
            ->assertSee($target->username)
            ->assertSee('Từ vai trò')
            ->set('permissions', [PermissionEnum::ScheduleCreate->value])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('users:refresh');

        $target->refresh();
        $this->assertTrue($target->hasDirectPermission(PermissionEnum::ScheduleCreate->value));
        $this->assertTrue($target->hasPermissionTo(PermissionEnum::ScheduleView->value));
        $this->assertTrue($target->hasPermissionTo(PermissionEnum::DashboardView->value));
        $this->assertTrue($target->hasPermissionTo(PermissionEnum::DocumentView->value));
        $this->assertFalse($colleague->fresh()->hasPermissionTo(PermissionEnum::ScheduleCreate->value));
        $this->assertSame([PermissionEnum::DocumentView->value], $role->fresh()->permissions->pluck('name')->all());

        Livewire::test(UserPermissions::class)
            ->call('open', $target->id)
            ->assertSet('permissions', fn ($permissions) => in_array(PermissionEnum::ScheduleCreate->value, $permissions, true))
            ->set('permissions', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse($target->fresh()->hasPermissionTo(PermissionEnum::ScheduleCreate->value));
        $this->assertTrue($target->fresh()->hasPermissionTo(PermissionEnum::DocumentView->value));
        $this->assertTrue($target->fresh()->hasRole($role));
    }

    public function test_user_update_permission_does_not_allow_managing_direct_permissions(): void
    {
        $actor = User::factory()->create();
        $actor->givePermissionTo(PermissionEnum::UserUpdate->value);
        $target = User::factory()->create();
        $this->actingAs($actor);

        Livewire::test(UserPermissions::class)->call('open', $target->id)->assertForbidden();
        Livewire::test(UserPermissions::class)
            ->set('permissions', [PermissionEnum::RoleManage->value])
            ->call('save')
            ->assertForbidden();

        $this->assertCount(0, $target->fresh()->permissions);
    }

    public function test_user_list_shows_permission_action_only_to_permission_managers(): void
    {
        $actor = User::factory()->create();
        $actor->givePermissionTo(PermissionEnum::UserView->value, PermissionEnum::RoleManage->value);
        $this->actingAs($actor)->get(route('users.index'))
            ->assertOk()->assertSee('Quyền riêng')->assertSee('id="userPermissionsModal"', false);

        $actor->revokePermissionTo(PermissionEnum::RoleManage->value);
        $this->get(route('users.index'))->assertOk()->assertDontSee('id="userPermissionsModal"', false);
    }

    public function test_saving_rechecks_authorization_and_rejects_unknown_or_wrong_guard_permissions(): void
    {
        $actor = User::factory()->create();
        $actor->givePermissionTo(PermissionEnum::RoleManage->value);
        $target = User::factory()->create();
        $this->actingAs($actor);

        Permission::create(['name' => 'api.only', 'guard_name' => 'api']);
        foreach (['unknown.permission', 'api.only'] as $permission) {
            Livewire::test(UserPermissions::class)
                ->call('open', $target->id)
                ->set('permissions', [$permission])
                ->call('save')
                ->assertHasErrors('permissions.0');
        }

        $component = Livewire::test(UserPermissions::class)->call('open', $target->id);
        $actor->revokePermissionTo(PermissionEnum::RoleManage->value);
        $component->call('save')->assertForbidden();
        $this->assertCount(0, $target->fresh()->permissions);
    }

    public function test_super_admin_permissions_are_protected_and_search_keeps_selected_permissions(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(RoleEnum::SuperAdmin->value);
        $target = User::factory()->create();
        $target->givePermissionTo(PermissionEnum::MailSend->value);
        $this->actingAs($actor);

        Livewire::test(UserPermissions::class)
            ->call('open', $target->id)
            ->set('search', 'cash-flow.export')
            ->assertSee('Xuất Excel báo cáo dòng tiền')
            ->assertSet('permissions', [PermissionEnum::MailSend->value]);

        Livewire::test(UserPermissions::class)
            ->call('open', $actor->id)
            ->assertSee('Super Admin luôn có toàn bộ quyền')
            ->assertDontSee('Lưu quyền riêng')
            ->set('permissions', [])
            ->call('save')
            ->assertForbidden();
    }
}
