<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentMethod;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Livewire\SalesTargets\SalesTargetIndex;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\SalesTarget;
use App\Models\User;
use App\Services\Reports\BusinessReportService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class BusinessReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_calculates_sales_kpi_collection_pipeline_and_conversion(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $customer = Customer::query()->create(['name' => 'Khách hàng KPI']);
        $contract = Contract::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Active,
            'title' => 'Hợp đồng KPI',
            'value' => 500_000_000,
            'signed_at' => '2026-07-04',
        ]);
        ContractPayment::query()->create([
            'contract_id' => $contract->id,
            'paid_at' => '2026-07-04',
            'amount' => 200_000_000,
            'payment_method' => PaymentMethod::BankTransfer,
        ]);
        SalesTarget::query()->create([
            'year' => 2026,
            'month' => 7,
            'user_id' => $sales->id,
            'target_amount' => 1_000_000_000,
            'target_contract_count' => 2,
        ]);
        Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::FollowingUp,
            'issued_at' => '2026-07-01',
            'contract_value' => 300_000_000,
        ]);
        foreach ([QuotationStatus::Won, QuotationStatus::Lost] as $status) {
            Quotation::query()->create([
                'customer_id' => $customer->id,
                'owner_id' => $sales->id,
                'contract_type' => ContractType::Consulting,
                'status' => $status,
                'issued_at' => '2026-07-02',
                'contract_value' => 100_000_000,
            ]);
        }

        $summary = app(BusinessReportService::class)->summary(2026, 7, $sales->id);

        self::assertSame(500_000_000, $summary['signed_value']);
        self::assertSame(200_000_000, $summary['collected']);
        self::assertSame(1_000_000_000, $summary['target']);
        self::assertSame(50.0, $summary['target_percent']);
        self::assertSame(300_000_000, $summary['pipeline']);
        self::assertSame(50.0, $summary['conversion_rate']);
    }

    public function test_sales_user_can_only_view_and_set_own_target(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $other = User::factory()->create();
        $other->assignRole(RoleEnum::Sales->value);
        $this->actingAs($sales);

        Livewire::test(SalesTargetIndex::class)
            ->set('targetUserId', $other->id)
            ->set('targetAmounts.7', '800000000')
            ->call('saveAnnualTargets')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $sales->id,
            'month' => 7,
            'target_amount' => 800_000_000,
        ]);
        $this->assertDatabaseMissing('sales_targets', [
            'user_id' => $other->id,
            'month' => 7,
        ]);
    }

    public function test_target_registration_form_prefills_existing_month_target(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        SalesTarget::query()->create([
            'year' => (int) now()->year,
            'month' => (int) now()->month,
            'user_id' => $sales->id,
            'target_amount' => 900_000_000,
            'target_contract_count' => 4,
            'notes' => 'Tập trung nhóm khách hàng tái ký.',
        ]);
        $this->actingAs($sales);

        Livewire::test(SalesTargetIndex::class)
            ->assertSee('Lưu cam kết')
            ->assertSee('Chi tiết cam kết theo tháng')
            ->assertSet('targetAmounts.7', '900000000');
    }

    public function test_sales_user_can_save_annual_target_commitments(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $this->actingAs($sales);

        $amounts = [];
        foreach (range(1, 12) as $month) {
            $amounts[$month] = (string) ($month * 100_000_000);
        }

        Livewire::test(SalesTargetIndex::class)
            ->set('targetAmounts', $amounts)
            ->call('saveAnnualTargets')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $sales->id,
            'month' => 1,
            'target_amount' => 100_000_000,
        ]);
        $this->assertDatabaseHas('sales_targets', [
            'user_id' => $sales->id,
            'month' => 12,
            'target_amount' => 1_200_000_000,
        ]);
        self::assertSame(12, SalesTarget::query()->where('user_id', $sales->id)->count());
    }
}
