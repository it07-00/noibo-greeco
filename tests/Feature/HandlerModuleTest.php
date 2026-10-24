<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Livewire\Contracts\ContractShow;
use App\Livewire\Handlers\HandlerIndex;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Handler;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class HandlerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_can_create_update_search_and_delete_subcontractors(): void
    {
        $this->admin();
        Livewire::test(HandlerIndex::class)
            ->call('openCreate')
            ->set('name', 'Đại Phú')->set('phone', '0901234567')->set('address', 'Long An')
            ->call('save')->assertHasNoErrors()->assertDispatched('handler-form:hide');
        $handler = Handler::where('name', 'Đại Phú')->firstOrFail();
        Handler::create(['name' => 'Nhà thầu khác', 'phone' => '0281234567']);

        Livewire::test(HandlerIndex::class)->set('search', '0901234567')->assertSee('Đại Phú')->assertDontSee('Nhà thầu khác');
        Livewire::test(HandlerIndex::class)->set('search', 'Long An')->assertSee('Đại Phú')->assertDontSee('Nhà thầu khác');
        Livewire::test(HandlerIndex::class)->call('openEdit', $handler->id)->set('name', 'Đại Phú mới')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('Đại Phú mới', $handler->fresh()->name);
        Livewire::test(HandlerIndex::class)->call('delete', $handler->id)->assertHasNoErrors();
        $this->assertSoftDeleted($handler);
    }

    public function test_validates_required_unique_name_and_field_lengths(): void
    {
        $this->admin();
        Handler::create(['name' => 'Đại Phú']);
        Livewire::test(HandlerIndex::class)->call('openCreate')->set('name', '   ')->call('save')->assertHasErrors('name');
        Livewire::test(HandlerIndex::class)->call('openCreate')->set('name', ' Đại Phú ')->call('save')->assertHasErrors('name');
        Livewire::test(HandlerIndex::class)->call('openCreate')->set('name', 'Mới')
            ->set('phone', str_repeat('1', 31))->set('address', str_repeat('a', 2001))
            ->call('save')->assertHasErrors(['phone', 'address']);
    }

    public function test_view_only_user_cannot_create_update_or_delete_even_by_calling_actions(): void
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo(PermissionEnum::HandlerView->value);
        $handler = Handler::create(['name' => 'Đại Phú']);
        $this->actingAs($viewer)->get(route('handlers.index'))->assertOk()->assertDontSee('Thêm nhà thầu phụ</button>', false);
        Livewire::test(HandlerIndex::class)->call('openCreate')->assertForbidden();
        Livewire::test(HandlerIndex::class)->call('openEdit', $handler->id)->assertForbidden();
        Livewire::test(HandlerIndex::class)->set('name', 'Trái phép')->call('save')->assertForbidden();
        Livewire::test(HandlerIndex::class)->call('delete', $handler->id)->assertForbidden();
        $viewer->revokePermissionTo(PermissionEnum::HandlerView->value);
        $this->get(route('handlers.index'))->assertForbidden();
    }

    public function test_create_permission_does_not_allow_editing_and_edit_permission_does_not_allow_creating(): void
    {
        $actor = User::factory()->create();
        $actor->givePermissionTo(PermissionEnum::HandlerView->value, PermissionEnum::HandlerUpdate->value);
        $handler = Handler::create(['name' => 'Đại Phú']);
        $this->actingAs($actor);
        Livewire::test(HandlerIndex::class)->set('name', 'Trái phép')->call('save')->assertForbidden();
        $component = Livewire::test(HandlerIndex::class)->call('openEdit', $handler->id);
        $actor->revokePermissionTo(PermissionEnum::HandlerUpdate->value);
        $actor->givePermissionTo(PermissionEnum::HandlerCreate->value);
        $component->set('name', 'Trái phép')->call('save')->assertForbidden();
        $this->assertSame('Đại Phú', $handler->fresh()->name);
    }

    public function test_cannot_delete_handler_with_current_or_archived_contracts(): void
    {
        $this->admin();
        $handler = Handler::create(['name' => 'Đại Phú']);
        $contract = $this->contract($handler);
        Livewire::test(HandlerIndex::class)->call('delete', $handler->id)->assertDispatched('swal:alert');
        $this->assertNotSoftDeleted($handler);
        $contract->delete();
        Livewire::test(HandlerIndex::class)->call('delete', $handler->id)->assertDispatched('swal:alert');
        $this->assertNotSoftDeleted($handler);
    }

    public function test_can_assign_change_and_clear_handler_on_contract_and_reject_invalid_selection(): void
    {
        $this->admin();
        $handler = Handler::create(['name' => 'Đại Phú']);
        $contract = $this->contract();
        $component = Livewire::test(ContractShow::class, ['contract' => $contract])
            ->call('openContractInfo')->set('contractHandlerId', (string) $handler->id)
            ->call('saveContractInfo')->assertHasNoErrors()->assertSee('Đại Phú');
        $this->assertSame($handler->id, $contract->fresh()->handler_id);
        $component->call('openContractInfo')->assertSet('contractHandlerId', (string) $handler->id)
            ->set('contractHandlerId', '999999')->call('saveContractInfo')->assertHasErrors('contractHandlerId');
        $this->assertSame($handler->id, $contract->fresh()->handler_id);
        $component->set('contractHandlerId', '')->call('saveContractInfo')->assertHasNoErrors();
        $this->assertNull($contract->fresh()->handler_id);
        $handler->delete();
        $component->set('contractHandlerId', (string) $handler->id)->call('saveContractInfo')->assertHasErrors('contractHandlerId');
    }

    public function test_contracts_page_only_shows_selected_handlers_contracts_and_requires_contract_access(): void
    {
        $admin = $this->admin();
        $handler = Handler::create(['name' => 'Đại Phú']);
        $other = Handler::create(['name' => 'Đơn vị khác']);
        $this->contract($handler, 'Hợp đồng Đại Phú');
        $this->contract($other, 'Hợp đồng bên khác');
        $this->get(route('handlers.contracts', $handler))->assertOk()->assertSee('Hợp đồng Đại Phú')->assertDontSee('Hợp đồng bên khác');
        $viewer = User::factory()->create();
        $viewer->givePermissionTo(PermissionEnum::HandlerView->value);
        $this->actingAs($viewer)->get(route('handlers.contracts', $handler))->assertForbidden();
        $this->actingAs($admin)->get(route('contracts.index'))->assertOk()->assertSee('Nhà thầu phụ: Đại Phú');
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SuperAdmin->value);
        $this->actingAs($user);

        return $user;
    }

    private function contract(?Handler $handler = null, string $title = 'Hợp đồng thử nghiệm'): Contract
    {
        return Contract::create([
            'customer_id' => Customer::create(['name' => 'Khách hàng thử nghiệm'])->id,
            'handler_id' => $handler?->id,
            'title' => $title,
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Draft,
            'value' => 1000000,
        ]);
    }
}
