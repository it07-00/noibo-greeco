<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentScheduleStatus;
use App\Enums\PaymentTermUnit;
use App\Enums\ServiceType;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractPaymentSchedule;
use App\Models\Customer;
use App\Services\Contracts\ContractWorkflowService;
use App\Services\Payments\PaymentAllocationService;
use App\Services\Payments\PaymentDueDateService;
use App\Services\Payments\PaymentPlanValidator;
use App\Services\Payments\PaymentScheduleStatusResolver;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContractPaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_state_machine_requires_services_plan_and_signed_date(): void
    {
        $contract = $this->createContract(100_000_000);
        $workflow = app(ContractWorkflowService::class);

        try {
            $workflow->transition($contract, ContractStatus::InternalReview);
            self::fail('Contract without services must not enter review.');
        } catch (DomainException $exception) {
            self::assertStringContainsString('ít nhất một dịch vụ', $exception->getMessage());
        }

        $contract->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'description' => 'Tư vấn ESG',
            'amount' => 100_000_000,
        ]);
        $this->createSchedule($contract, 1, 100_000_000, '100.00');

        $contract = $workflow->transition($contract, ContractStatus::InternalReview);
        $contract = $workflow->transition($contract, ContractStatus::WaitingCustomerSignature);

        try {
            $workflow->transition($contract, ContractStatus::Active);
            self::fail('Unsigned contract must not be activated.');
        } catch (DomainException $exception) {
            self::assertStringContainsString('ngày ký', $exception->getMessage());
        }

        $contract->update(['signed_at' => '2026-07-04']);
        $contract = $workflow->transition($contract->refresh(), ContractStatus::Active);

        self::assertSame(ContractStatus::Active, $contract->status);
    }

    public function test_dynamic_payment_plan_must_match_contract_value_and_percentage(): void
    {
        $contract = $this->createContract(1_000_000_000);
        $this->createSchedule($contract, 1, 300_000_000, '30.00');
        $this->createSchedule($contract, 2, 400_000_000, '40.00');
        $this->createSchedule($contract, 3, 300_000_000, '30.00');

        app(PaymentPlanValidator::class)->validate($contract);

        self::assertTrue(true);
    }

    public function test_invalid_payment_plan_is_rejected(): void
    {
        $contract = $this->createContract(1_000_000_000);
        $this->createSchedule($contract, 1, 300_000_000, '30.00');
        $this->createSchedule($contract, 2, 600_000_000, '60.00');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('bằng giá trị hợp đồng');

        app(PaymentPlanValidator::class)->validate($contract);
    }

    public function test_due_date_is_calculated_only_after_condition_is_triggered(): void
    {
        $contract = $this->createContract(100_000_000);
        $schedule = $this->createSchedule($contract, 1, 100_000_000, '100.00', [
            'payment_term_days' => 1,
            'payment_term_unit' => PaymentTermUnit::BusinessDays,
        ]);

        self::assertNull($schedule->due_date);
        self::assertSame(
            PaymentScheduleStatus::WaitingCondition,
            app(PaymentScheduleStatusResolver::class)->resolve($schedule),
        );

        $schedule = app(PaymentDueDateService::class)->trigger(
            $schedule,
            CarbonImmutable::parse('2026-07-03'),
        );

        self::assertSame('2026-07-06', $schedule->due_date?->toDateString());
        self::assertSame('2026-07-03', $schedule->triggered_at?->toDateString());
    }

    public function test_one_payment_can_be_allocated_to_multiple_installments(): void
    {
        $contract = $this->createContract(1_000_000_000);
        $first = $this->createSchedule($contract, 1, 300_000_000, '30.00');
        $second = $this->createSchedule($contract, 2, 700_000_000, '70.00', [
            'triggered_at' => '2026-07-01',
        ]);
        $payment = ContractPayment::query()->create([
            'contract_id' => $contract->id,
            'paid_at' => '2026-07-04',
            'amount' => 500_000_000,
            'payment_method' => PaymentMethod::BankTransfer,
            'reference_number' => 'BANK-001',
        ]);
        $service = app(PaymentAllocationService::class);

        $service->allocate($payment, $first, 300_000_000);
        $service->allocate($payment, $second, 200_000_000);

        self::assertSame(0, $service->unallocatedAmount($payment));
        self::assertSame(PaymentScheduleStatus::Paid, $first->refresh()->status);
        self::assertSame(PaymentScheduleStatus::PartiallyPaid, $second->refresh()->status);
    }

    public function test_payment_can_remain_partially_unallocated_as_advance(): void
    {
        $contract = $this->createContract(1_000_000_000);
        $schedule = $this->createSchedule($contract, 1, 300_000_000, '30.00');
        $payment = ContractPayment::query()->create([
            'contract_id' => $contract->id,
            'paid_at' => '2026-07-04',
            'amount' => 500_000_000,
            'payment_method' => PaymentMethod::BankTransfer,
        ]);
        $service = app(PaymentAllocationService::class);

        $service->allocate($payment, $schedule, 300_000_000);

        self::assertSame(200_000_000, $service->unallocatedAmount($payment));
    }

    public function test_payment_cannot_be_allocated_across_contracts(): void
    {
        $firstContract = $this->createContract(100_000_000);
        $secondContract = $this->createContract(100_000_000);
        $schedule = $this->createSchedule($secondContract, 1, 100_000_000, '100.00');
        $payment = ContractPayment::query()->create([
            'contract_id' => $firstContract->id,
            'paid_at' => '2026-07-04',
            'amount' => 100_000_000,
            'payment_method' => PaymentMethod::BankTransfer,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('hợp đồng khác');

        app(PaymentAllocationService::class)->allocate($payment, $schedule, 100_000_000);
    }

    private function createContract(int $value): Contract
    {
        $customer = Customer::query()->create([
            'name' => fake()->company(),
            'tax_code' => fake()->unique()->numerify('##########'),
        ]);

        return Contract::query()->create([
            'customer_id' => $customer->id,
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Draft,
            'title' => 'Hợp đồng thử nghiệm',
            'value' => $value,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createSchedule(
        Contract $contract,
        int $installment,
        int $amount,
        string $percentage,
        array $overrides = [],
    ): ContractPaymentSchedule {
        return ContractPaymentSchedule::query()->create(array_merge([
            'contract_id' => $contract->id,
            'installment_number' => $installment,
            'name' => "Đợt {$installment}",
            'percentage' => $percentage,
            'amount' => $amount,
            'condition_type' => PaymentConditionType::AfterContractSigned,
        ], $overrides));
    }
}
