<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CommissionRequestStatus;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentMethod;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Livewire\Commissions\CommissionRequestForm;
use App\Livewire\Commissions\CommissionRequestIndex;
use App\Models\CommissionRequest;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class CommissionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_create_commission_request_with_vietqr_details(): void
    {
        Http::fake([
            'api.vietqr.io/v2/banks' => Http::response([
                'data' => [
                    ['bin' => '970436', 'code' => 'VCB', 'shortName' => 'Vietcombank'],
                ],
            ]),
        ]);

        [$contract, $sales] = $this->contractForSales();
        $this->actingAs($sales);

        Livewire::test(CommissionRequestForm::class)
            ->set('contractType', ContractType::Consulting->value)
            ->set('contractId', (string) $contract->id)
            ->set('receiverName', 'Nguyễn Văn A')
            ->set('receiverPhone', '0909000000')
            ->set('bankCode', '970436')
            ->set('bankNumber', '123456789')
            ->set('amount', '5000000')
            ->set('referrerInfo', 'Khách giới thiệu')
            ->call('save')
            ->assertHasNoErrors();

        $request = CommissionRequest::query()->firstOrFail();

        self::assertSame($contract->id, $request->contract_id);
        self::assertSame('NGUYEN VAN A', $request->receiver_name);
        self::assertSame(5_000_000, $request->amount);
        self::assertSame(CommissionRequestStatus::Estimated, $request->status);
        self::assertStringContainsString('https://img.vietqr.io/image/970436-123456789-compact2.png', $request->qr_url);
        self::assertStringContainsString('amount=5000000', $request->qr_url);
    }

    public function test_accountant_can_approve_and_mark_commission_request_paid(): void
    {
        Storage::fake('local');

        [$contract, $sales] = $this->contractForSales();
        $accountant = $this->userWithRole(RoleEnum::Accountant);
        $request = CommissionRequest::query()->create([
            'contract_id' => $contract->id,
            'user_id' => $sales->id,
            'receiver_name' => 'NGUYEN VAN A',
            'bank_code' => '970436',
            'bank_number' => '123456789',
            'amount' => 5_000_000,
            'status' => CommissionRequestStatus::Estimated,
        ]);

        $this->actingAs($accountant);

        $file = UploadedFile::fake()->create('bill.pdf', 100);

        Livewire::test(CommissionRequestIndex::class)
            ->call('approve', $request->id)
            ->assertHasNoErrors()
            ->call('startPay', $request->id)
            ->assertHasNoErrors()
            ->set('paymentBillFile', $file)
            ->call('confirmPay')
            ->assertHasNoErrors();

        self::assertSame(CommissionRequestStatus::Paid, $request->refresh()->status);
        self::assertSame($accountant->id, $request->processed_by);
        self::assertNotNull($request->processed_at);
        self::assertNotNull($request->payment_bill_path);
        Storage::disk('local')->assertExists($request->payment_bill_path);
    }

    public function test_sales_cannot_approve_commission_request(): void
    {
        [$contract, $sales] = $this->contractForSales();
        $request = CommissionRequest::query()->create([
            'contract_id' => $contract->id,
            'user_id' => $sales->id,
            'receiver_name' => 'NGUYEN VAN A',
            'amount' => 5_000_000,
            'status' => CommissionRequestStatus::Estimated,
        ]);

        $this->actingAs($sales);

        Livewire::test(CommissionRequestIndex::class)
            ->call('approve', $request->id)
            ->assertForbidden();
    }

    /**
     * @return array{0: Contract, 1: User}
     */
    private function contractForSales(): array
    {
        $sales = $this->userWithRole(RoleEnum::Sales);
        $customer = Customer::query()->create(['name' => fake()->company()]);

        $contract = Contract::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'department_id' => $sales->department_id,
            'contract_number' => 'HD-001',
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Active,
            'title' => 'Hợp đồng tư vấn ESG',
            'value' => 100_000_000,
            'customer_commission' => 5_000_000,
            'commission_tax' => 500_000,
            'payment_method' => PaymentMethod::BankTransfer,
        ]);

        $contract->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'description' => 'Tư vấn ESG',
            'amount' => 100_000_000,
        ]);

        return [$contract, $sales];
    }

    private function userWithRole(RoleEnum $role): User
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user->refresh();
    }
}
