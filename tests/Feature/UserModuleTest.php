<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTOs\UserDTO;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\UserService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class UserModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_seeder_creates_default_roles_permissions_and_super_admin(): void
    {
        $this->seed(PermissionSeeder::class);

        foreach (PermissionEnum::cases() as $permission) {
            $this->assertDatabaseHas('permissions', [
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        foreach (RoleEnum::cases() as $role) {
            $this->assertDatabaseHas('roles', [
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = User::query()->where('email', 'superadmin@example.com')->firstOrFail();

        $this->assertTrue($superAdmin->hasRole(RoleEnum::SuperAdmin->value));
    }

    public function test_authorized_user_can_access_user_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->givePermissionTo(PermissionEnum::DashboardView->value, PermissionEnum::UserView->value);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_user_service_creates_updates_and_soft_deletes_user(): void
    {
        $this->seed(PermissionSeeder::class);

        $service = app(UserService::class);

        $user = $service->create(UserDTO::fromArray([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'password',
            'roles' => [RoleEnum::Director->value],
        ]));

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertTrue($user->hasRole(RoleEnum::Director->value));

        $updated = $service->update($user, UserDTO::fromArray([
            'name' => 'Admin Member',
            'email' => 'admin.member@example.com',
            'roles' => [RoleEnum::IT->value],
        ]));

        $this->assertSame('Admin Member', $updated->name);
        $this->assertSame('admin.member@example.com', $updated->email);
        $this->assertTrue($updated->hasRole(RoleEnum::IT->value));

        $service->delete($updated);

        $this->assertSoftDeleted('users', [
            'id' => $updated->id,
        ]);
    }

    public function test_director_role_does_not_have_user_delete_permission(): void
    {
        $this->seed(PermissionSeeder::class);

        $permission = Permission::query()
            ->where('name', PermissionEnum::UserDelete->value)
            ->firstOrFail();

        $role = Role::findByName(RoleEnum::Director->value);

        $this->assertFalse($role->hasPermissionTo($permission));
    }
}
