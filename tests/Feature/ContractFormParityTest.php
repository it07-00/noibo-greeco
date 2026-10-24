<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Livewire\Contracts\ContractForm;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Handler;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class ContractFormParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_can_create_directly_with_handler_and_business_fields_and_reopen(): void
    {
        $actor = $this->actor();
        $customer = Customer::create(['name' => 'Khách hàng']);
        $handler = Handler::create(['name' => 'Đại Phú']);
        Livewire::test(ContractForm::class)->set('form.customer_id', $customer->id)
            ->set('form.handler_id', $handler->id)->set('form.title', 'Hợp đồng trực tiếp')
            ->set('form.contract_number', 'GRC-001')->set('form.value', 1000000)
            ->set('form.province', 'Long An')->set('form.submission_place', 'Sở Công thương')
            ->set('form.report_number', 'BC-001')->set('form.voucher_status', 'invoiced')
            ->set('form.contract_drive_url', 'https://drive.google.com/drive/folders/example')
            ->set('serviceRows', $this->services())->call('save')->assertHasNoErrors();
        $contract = Contract::where('contract_number', 'GRC-001')->firstOrFail();
        $this->assertNull($contract->quotation_id);
        $this->assertSame($handler->id, $contract->handler_id);
        $this->assertSame('invoiced', $contract->voucher_status->value);
        $this->assertCount(1, $contract->services);
        Livewire::test(ContractForm::class, ['contractId' => $contract->id])
            ->assertSet('form.submission_place', 'Sở Công thương')->assertSet('form.owner_id', $actor->id);
        $this->get(route('contracts.edit', $contract->id))->assertOk()->assertSee('Cập nhật hợp đồng');
    }

    public function test_inline_customer_creation_requires_customer_permission_and_remains_atomic(): void
    {
        $actor = $this->actor();
        Livewire::test(ContractForm::class)->set('newCustomerName', 'Công ty mới')
            ->set('form.title', 'Hợp đồng mới')->set('serviceRows', $this->services())
            ->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('customers', ['name' => 'Công ty mới']);
        $actor->syncRoles([]);
        $actor->givePermissionTo(['contract.create', 'contract.view']);
        Livewire::test(ContractForm::class)->set('newCustomerName', 'Công ty trái phép')
            ->set('form.title', 'Hợp đồng mới')->set('serviceRows', $this->services())
            ->call('save')->assertForbidden();
        $this->assertDatabaseMissing('customers', ['name' => 'Công ty trái phép']);
    }

    public function test_duplicate_resets_identity_and_does_not_copy_payments_assignments_or_workflow(): void
    {
        $actor = $this->actor();
        $contract = $this->contract($actor);
        $contract->services()->create($this->services()[0]);
        $contract->assignments()->create(['user_id' => $actor->id, 'assigned_by' => $actor->id]);
        $contract->workflowSteps()->create(['step_name' => 'receiving', 'user_id' => $actor->id]);
        $this->get(route('contracts.create', ['copy' => $contract->id]))->assertOk()->assertSee('Thêm hợp đồng');
        // A fresh form has no source IDs or operational children; only the selected business data is persisted.
        Livewire::withQueryParams(['copy' => $contract->id])->test(ContractForm::class)
            ->assertSet('contractId', null)->assertSet('form.contract_number', '')
            ->assertSet('form.status', 'draft')->set('form.contract_number', 'COPY-001')
            ->call('save')->assertHasNoErrors();
        $copy = Contract::where('contract_number', 'COPY-001')->firstOrFail();
        $this->assertSame(0, $copy->assignments()->count());
        $this->assertSame(0, $copy->workflowSteps()->count());
        $this->assertSame(0, $copy->payments()->count());
        $this->assertSame(1, $copy->services()->count());
    }

    public function test_service_type_must_match_contract_and_existing_services_cannot_be_stolen(): void
    {
        $actor = $this->actor();
        $customer = Customer::create(['name' => 'Khách hàng']);
        Livewire::test(ContractForm::class)->set('form.customer_id', $customer->id)
            ->set('form.title', 'Sai dịch vụ')->set('form.type', ContractType::Training->value)
            ->set('serviceRows', $this->services())->call('save')->assertHasErrors('serviceRows');
        $this->assertDatabaseMissing('contracts', ['title' => 'Sai dịch vụ']);
        $contract = $this->contract($actor);
        $other = $this->contract($actor);
        $service = $other->services()->create($this->services()[0]);
        Livewire::test(ContractForm::class, ['contractId' => $contract->id])
            ->set('serviceRows', [['id' => $service->id, ...$this->services()[0]]])
            ->call('save')->assertHasErrors('serviceRows.0.id');
        $this->assertSame($other->id, $service->fresh()->contract_id);
    }

    public function test_consultant_cannot_overwrite_financial_values_in_tampered_form(): void
    {
        $sales = $this->actor();
        $contract = $this->contract($sales);
        $service = $contract->services()->create($this->services()[0]);
        $consultant = User::factory()->create();
        $consultant->assignRole(RoleEnum::Consultant->value);
        $contract->assignments()->create(['user_id' => $consultant->id, 'assigned_by' => $sales->id]);
        $this->actingAs($consultant);
        Livewire::test(ContractForm::class, ['contractId' => $contract->id])
            ->assertSet('form.value', 0)->set('form.value', 1)->set('form.customer_commission', 999999)
            ->set('serviceRows.0.amount', 1)->set('form.title', 'Ghi chú nghiệp vụ')
            ->call('save')->assertHasNoErrors();
        $this->assertSame(1000000, $contract->fresh()->value);
        $this->assertSame(1000000, $service->fresh()->amount);
        $this->assertSame(0, $contract->fresh()->customer_commission);
    }

    public function test_payment_percentage_uses_original_base_and_invalid_dates_or_urls_are_rejected(): void
    {
        $this->actor();
        Livewire::test(ContractForm::class)->set('form.value', 1000000)->set('form.revenue', 800000)
            ->set('form.payment_percentage', 50)->assertSet('form.value', 500000)->assertSet('form.revenue', 400000)
            ->set('form.payment_percentage', 100)->assertSet('form.value', 1000000)->assertSet('form.revenue', 800000);
        Livewire::test(ContractForm::class)->set('form.starts_at', '2026-10-24')
            ->set('form.ends_at', '2026-10-23')->set('form.contract_drive_url', 'javascript:alert(1)')
            ->call('save')->assertHasErrors(['form.ends_at', 'form.contract_drive_url']);
    }

    public function test_renewal_parent_cannot_form_a_cycle_or_reference_an_unviewable_contract(): void
    {
        $actor = $this->actor();
        $first = $this->contract($actor);
        $second = $this->contract($actor);
        $first->update(['is_renewal' => true, 'parent_contract_id' => $second->id]);
        Livewire::test(ContractForm::class, ['contractId' => $second->id])
            ->set('serviceRows', $this->services())->set('form.is_renewal', true)
            ->set('form.parent_contract_id', $first->id)->call('save')->assertHasErrors('form.parent_contract_id');
        $other = User::factory()->create();
        $other->assignRole(RoleEnum::Sales->value);
        $private = $this->contract($other);
        Livewire::test(ContractForm::class, ['contractId' => $second->id])
            ->set('serviceRows', $this->services())->set('form.is_renewal', true)
            ->set('form.parent_contract_id', $private->id)->call('save')->assertForbidden();
        $this->assertNull($second->fresh()->parent_contract_id);
    }

    private function actor(): User
    {
        $actor = User::factory()->create();
        $actor->assignRole(RoleEnum::Sales->value);
        $this->actingAs($actor);

        return $actor;
    }

    private function services(): array
    {
        return [['service_type' => ServiceType::EsgConsulting->value, 'description' => 'Tư vấn ESG', 'amount' => 1000000]];
    }

    private function contract(User $owner): Contract
    {
        return Contract::create(['customer_id' => Customer::create(['name' => 'Khách hàng'])->id, 'owner_id' => $owner->id,
            'title' => 'Hợp đồng gốc', 'type' => ContractType::Consulting, 'status' => ContractStatus::Draft, 'value' => 1000000]);
    }
}
