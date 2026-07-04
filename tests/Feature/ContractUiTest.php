<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentHandoverStatus;
use App\Enums\PaymentMethod;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Livewire\Contracts\ContractShow;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class ContractUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_create_and_submit_dynamic_payment_plan(): void
    {
        [$contract, $sales] = $this->contractForSales(1_000_000_000);
        $this->actingAs($sales);

        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->set('scheduleName', 'Tạm ứng')
            ->set('schedulePercentage', '30')
            ->set('scheduleAmount', '300000000')
            ->set('scheduleConditionType', PaymentConditionType::AfterContractSigned->value)
            ->call('saveSchedule')
            ->assertHasNoErrors()
            ->set('scheduleName', 'Nghiệm thu')
            ->set('schedulePercentage', '70')
            ->set('scheduleAmount', '700000000')
            ->set('scheduleConditionType', PaymentConditionType::AfterAcceptance->value)
            ->call('saveSchedule')
            ->assertHasNoErrors()
            ->call('submitPaymentPlan')
            ->assertHasNoErrors();

        self::assertSame(2, $contract->paymentSchedules()->count());
        self::assertFalse(
            $contract->paymentSchedules()
                ->where('handover_status', '!=', PaymentHandoverStatus::SubmittedToAccounting->value)
                ->exists(),
        );
    }

    public function test_accounting_can_confirm_plan_and_record_one_payment_for_multiple_installments(): void
    {
        [$contract] = $this->contractForSales(1_000_000_000);
        $accountant = $this->userWithRole(RoleEnum::Accountant);
        $first = $this->schedule($contract, 1, 300_000_000, '30.00');
        $second = $this->schedule($contract, 2, 700_000_000, '70.00');
        $this->actingAs($accountant);

        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->call('confirmPaymentPlan')
            ->assertHasNoErrors()
            ->set('paymentPaidAt', '2026-07-04')
            ->set('paymentAmount', '500000000')
            ->set('paymentMethod', PaymentMethod::BankTransfer->value)
            ->set('paymentReference', 'BANK-500')
            ->set('allocationRows', [
                ['payment_schedule_id' => $first->id, 'amount' => '300000000'],
                ['payment_schedule_id' => $second->id, 'amount' => '200000000'],
            ])
            ->call('recordPayment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contract_payments', [
            'contract_id' => $contract->id,
            'amount' => 500_000_000,
            'reference_number' => 'BANK-500',
        ]);
        self::assertSame(300_000_000, (int) $first->allocations()->sum('allocated_amount'));
        self::assertSame(200_000_000, (int) $second->allocations()->sum('allocated_amount'));
        self::assertNotNull($first->refresh()->confirmed_at);
        self::assertNotNull($second->refresh()->confirmed_at);
    }

    public function test_sales_cannot_record_customer_payment(): void
    {
        [$contract, $sales] = $this->contractForSales(100_000_000);
        $this->actingAs($sales);

        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->set('paymentPaidAt', '2026-07-04')
            ->set('paymentAmount', '100000000')
            ->set('paymentMethod', PaymentMethod::BankTransfer->value)
            ->call('recordPayment')
            ->assertForbidden();
    }

    public function test_sales_can_update_contract_renewal_status(): void
    {
        [$contract, $sales] = $this->contractForSales(100_000_000);
        $this->actingAs($sales);

        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->call('openContractInfo')
            ->assertSet('contractRenewalStatus', 'not_applicable')
            ->set('contractRenewalStatus', 'pending')
            ->call('saveContractInfo')
            ->assertHasNoErrors();

        self::assertSame('pending', $contract->refresh()->renewal_status->value);
    }

    private function contractForSales(int $value): array
    {
        $sales = $this->userWithRole(RoleEnum::Sales);
        $customer = Customer::query()->create(['name' => fake()->company()]);
        $contract = Contract::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'department_id' => $sales->department_id,
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Draft,
            'title' => 'Hợp đồng tư vấn ESG',
            'value' => $value,
        ]);
        $contract->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'description' => 'Tư vấn ESG',
            'amount' => $value,
        ]);

        return [$contract, $sales];
    }

    private function schedule(
        Contract $contract,
        int $installment,
        int $amount,
        string $percentage,
    ): ContractPaymentSchedule {
        return ContractPaymentSchedule::query()->create([
            'contract_id' => $contract->id,
            'installment_number' => $installment,
            'name' => "Đợt {$installment}",
            'percentage' => $percentage,
            'amount' => $amount,
            'condition_type' => PaymentConditionType::AfterContractSigned,
            'handover_status' => PaymentHandoverStatus::SubmittedToAccounting,
        ]);
    }

    private function userWithRole(RoleEnum $role): User
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user->refresh();
    }
}
