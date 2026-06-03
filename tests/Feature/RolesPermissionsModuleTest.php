<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\RolePermissionService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class RolesPermissionsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_roles_permissions(): void
    {
        $this->get(route('roles-permissions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_roles_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $staff = User::factory()->create();
        $staff->assignRole(RoleEnum::Director->value); // Director does not have role.manage

        $this->actingAs($staff)
            ->get(route('roles-permissions.index'))
            ->assertStatus(403);
    }

    public function test_authorized_user_can_access_roles_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value); // Admin has role.manage

        $this->actingAs($admin)
            ->get(route('roles-permissions.index'))
            ->assertOk();
    }

    public function test_component_mounts_with_first_role_as_active(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        $firstRole = Role::query()->orderBy('id')->first();

        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->assertSet('activeRoleId', $firstRole->id);
    }

    public function test_can_select_different_role(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        $roles = Role::query()->orderBy('id')->get();
        $secondRole = $roles[1];

        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('selectRole', $secondRole->id)
            ->assertSet('activeRoleId', $secondRole->id);
    }

    public function test_can_create_new_role_with_validation(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        // Test validation empty name
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->set('newRoleName', '')
            ->call('createRole')
            ->assertHasErrors(['newRoleName' => 'required']);

        // Test creation success
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->set('newRoleName', 'Custom Editor')
            ->set('newRoleDescription', 'Custom role description')
            ->call('createRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'name' => 'Custom Editor',
            'description' => 'Custom role description',
        ]);
    }

    public function test_can_toggle_permission_on_role(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        // Find standard non-SuperAdmin role
        $itRole = Role::findByName(RoleEnum::IT->value);

        // Mount and toggle ScheduleCreate permission
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('selectRole', $itRole->id)
            ->call('togglePermission', app(RolePermissionService::class), PermissionEnum::ScheduleCreate->value)
            ->assertHasNoErrors();

        // Since it starts assigned in the seeder, toggling it should remove it
        $this->assertFalse($itRole->refresh()->hasPermissionTo(PermissionEnum::ScheduleCreate->value));

        // Toggle again to add it back
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('selectRole', $itRole->id)
            ->call('togglePermission', app(RolePermissionService::class), PermissionEnum::ScheduleCreate->value)
            ->assertHasNoErrors();

        $this->assertTrue($itRole->refresh()->hasPermissionTo(PermissionEnum::ScheduleCreate->value));
    }

    public function test_cannot_toggle_super_admin_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        $superAdminRole = Role::findByName(RoleEnum::SuperAdmin->value);

        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('selectRole', $superAdminRole->id)
            ->call('togglePermission', app(RolePermissionService::class), PermissionEnum::UserCreate->value)
            ->assertHasNoErrors();

        // Super Admin still retains the permission
        $this->assertTrue($superAdminRole->refresh()->hasPermissionTo(PermissionEnum::UserCreate->value));
    }

    public function test_can_delete_custom_role_but_cannot_delete_super_admin(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin);

        // Create custom role first
        $customRole = Role::create([
            'name' => 'Temporary Role',
            'guard_name' => 'web',
        ]);

        // Delete custom role
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('deleteRole', app(RolePermissionService::class), $customRole->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('roles', ['id' => $customRole->id]);

        // Attempt delete Super Admin
        $superAdminRole = Role::findByName(RoleEnum::SuperAdmin->value);

        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('deleteRole', app(RolePermissionService::class), $superAdminRole->id)
            ->assertHasNoErrors();

        // Super Admin role must still exist
        $this->assertDatabaseHas('roles', ['id' => $superAdminRole->id]);
    }
}
