<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\DocumentRegulation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class DocumentRegulationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_access_document_regulations_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $director = User::factory()->create();
        $director->assignRole(RoleEnum::Director->value);

        $this->actingAs($director)
            ->get(route('document-regulations.index'))
            ->assertOk()
            ->assertSee('Quy định Tài liệu');
    }

    public function test_unauthorized_user_cannot_access_document_regulations_index(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('document-regulations.index'))
            ->assertStatus(403);
    }

    public function test_user_without_manage_permission_cannot_create_or_delete_regulations(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::DocumentView->value);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\DocumentRegulations\DocumentRegulationIndex::class)
            ->assertSet('canManage', false)
            ->call('openCreate')
            ->assertStatus(403);
    }

    public function test_authorized_user_can_crud_regulations(): void
    {
        $this->seed(PermissionSeeder::class);
        Storage::fake('public');

        $director = User::factory()->create();
        $director->assignRole(RoleEnum::Director->value);

        $this->actingAs($director);

        $itRole = Role::where('name', RoleEnum::IT->value)->firstOrFail();

        // 1. Create Regulation
        $file = UploadedFile::fake()->create('doc01.pdf', 500);

        Livewire::test(\App\Livewire\DocumentRegulations\DocumentRegulationIndex::class)
            ->assertSet('canManage', true)
            ->set('code', 'QD-TL-TEST')
            ->set('title', 'Quy định thử nghiệm')
            ->set('roleId', $itRole->id)
            ->set('status', 'active')
            ->set('summary', 'Tóm tắt quy định thử nghiệm')
            ->set('content', 'Chi tiết toàn văn quy định thử nghiệm')
            ->set('file', $file)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('document:saved');

        $this->assertDatabaseHas('document_regulations', [
            'code' => 'QD-TL-TEST',
            'title' => 'Quy định thử nghiệm',
            'role_id' => $itRole->id,
            'created_by' => $director->id,
        ]);

        $regulation = DocumentRegulation::where('code', 'QD-TL-TEST')->firstOrFail();
        $this->assertNotNull($regulation->file_path);
        Storage::disk('public')->assertExists($regulation->file_path);

        // 2. Update Regulation
        Livewire::test(\App\Livewire\DocumentRegulations\DocumentRegulationIndex::class)
            ->call('openEdit', $regulation->id)
            ->assertSet('code', 'QD-TL-TEST')
            ->assertSet('roleId', $itRole->id)
            ->set('title', 'Quy định thử nghiệm cập nhật')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('document_regulations', [
            'id' => $regulation->id,
            'title' => 'Quy định thử nghiệm cập nhật',
        ]);

        // 3. Delete Regulation
        Livewire::test(\App\Livewire\DocumentRegulations\DocumentRegulationIndex::class)
            ->call('delete', $regulation->id)
            ->assertDispatched('document:deleted');

        $this->assertDatabaseMissing('document_regulations', [
            'id' => $regulation->id,
        ]);
        Storage::disk('public')->assertMissing($regulation->file_path);
    }
}
