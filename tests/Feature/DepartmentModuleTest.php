<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class DepartmentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_access_departments_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);

        $this->actingAs($admin)
            ->get(route('departments.index'))
            ->assertOk()
            ->assertSee('Quản lý Phòng ban');
    }

    public function test_unauthorized_user_cannot_access_departments_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::Director->value); // Director does not have role.manage

        $this->actingAs($user)
            ->get(route('departments.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_crud_departments(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);
        $this->actingAs($admin);

        // 1. Create Department
        Livewire::test(\App\Livewire\Departments\DepartmentIndex::class)
            ->set('code', 'MKT')
            ->set('name', 'Marketing Department')
            ->set('description', 'Marketing and communications')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('department:saved');

        $this->assertDatabaseHas('departments', [
            'code' => 'MKT',
            'name' => 'Marketing Department',
        ]);

        $dept = Department::where('code', 'MKT')->firstOrFail();

        // 2. Update Department
        Livewire::test(\App\Livewire\Departments\DepartmentIndex::class)
            ->call('openEdit', $dept->id)
            ->assertSet('code', 'MKT')
            ->set('name', 'Marketing Updated')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
            'name' => 'Marketing Updated',
        ]);

        // 3. Delete Department
        Livewire::test(\App\Livewire\Departments\DepartmentIndex::class)
            ->call('delete', $dept->id)
            ->assertDispatched('department:deleted');

        $this->assertDatabaseMissing('departments', [
            'id' => $dept->id,
        ]);
    }

    public function test_admin_can_link_role_to_department(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SuperAdmin->value);
        $this->actingAs($admin);

        $dept = Department::where('code', 'IT')->firstOrFail();

        // Create role with department
        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->set('newRoleName', 'IT Security Engineer')
            ->set('newRoleDescription', 'Handles IT security operations')
            ->set('newRoleDepartmentId', $dept->id)
            ->call('createRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'name' => 'IT Security Engineer',
            'department_id' => $dept->id,
        ]);

        $role = Role::findByName('IT Security Engineer');

        // Edit role department
        $hrDept = Department::where('code', 'HCNS')->firstOrFail();

        Livewire::test(\App\Livewire\RolesPermissions\RolesPermissionsIndex::class)
            ->call('openEditModal', $role->id)
            ->set('editRoleDepartmentId', $hrDept->id)
            ->call('updateRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'department_id' => $hrDept->id,
        ]);
    }
}
