<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractType;
use App\Enums\CustomerType;
use App\Enums\DocumentType;
use App\Enums\PaymentConditionType;
use App\Enums\PaymentTermUnit;
use App\Enums\QuotationStatus;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Quotations\QuotationIndex;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class SalesUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_user_can_access_customer_and_quotation_pages(): void
    {
        $sales = $this->salesUser();

        $this->actingAs($sales)
            ->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Khách hàng');

        $this->actingAs($sales)
            ->get(route('quotations.index'))
            ->assertOk()
            ->assertSee('Theo dõi báo giá');
    }

    public function test_sales_user_can_create_customer_from_livewire_form(): void
    {
        $this->actingAs($this->salesUser());

        Livewire::test(CustomerIndex::class)
            ->set('name', 'Công ty Xanh Việt')
            ->set('taxCode', '0312345678')
            ->set('contactName', 'Nguyễn Văn An')
            ->set('phone', '0901234567')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Công ty Xanh Việt',
            'tax_code' => '0312345678',
        ]);
    }

    public function test_sales_user_can_create_and_filter_individual_course_customer(): void
    {
        $this->actingAs($this->salesUser());

        Livewire::test(CustomerIndex::class)
            ->set('customerType', CustomerType::Individual->value)
            ->set('name', 'Nguyễn Minh Anh')
            ->set('phone', '0909999999')
            ->set('email', 'minhanh@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Nguyễn Minh Anh',
            'type' => CustomerType::Individual->value,
            'phone' => '0909999999',
        ]);

        Customer::query()->create([
            'name' => 'Công ty Chỉ Hiện Ở Nhóm Tổ Chức',
            'type' => CustomerType::Organization,
        ]);

        Livewire::test(CustomerIndex::class)
            ->set('typeFilter', CustomerType::Individual->value)
            ->assertSee('Nguyễn Minh Anh')
            ->assertDontSee('Công ty Chỉ Hiện Ở Nhóm Tổ Chức');
    }

    public function test_sales_user_can_lookup_tax_code_successfully(): void
    {
        $this->actingAs($this->salesUser());

        Http::fake([
            'api.vietqr.io/v2/business/*' => Http::response([
                'code' => '00',
                'desc' => 'success',
                'data' => [
                    'id' => '0316794479',
                    'name' => 'CÔNG TY TNHH GREEN CO',
                    'address' => '123 Đường Số 1, Phường 2, Quận 3, TP. Hồ Chí Minh',
                ],
            ], 200),
        ]);

        Livewire::test(CustomerIndex::class)
            ->set('taxCode', '0316794479')
            ->call('lookupTaxCode')
            ->assertSet('name', 'CÔNG TY TNHH GREEN CO')
            ->assertSet('billingAddress', '123 Đường Số 1, Phường 2, Quận 3, TP. Hồ Chí Minh')
            ->assertSet('province', 'TP. Hồ Chí Minh')
            ->assertHasNoErrors();
    }

    public function test_sales_user_lookup_tax_code_handles_failure(): void
    {
        $this->actingAs($this->salesUser());

        Http::fake([
            'api.vietqr.io/v2/business/*' => Http::response([
                'code' => '99',
                'desc' => 'Không tìm thấy thông tin doanh nghiệp',
            ], 200),
        ]);

        Livewire::test(CustomerIndex::class)
            ->set('taxCode', '9999999999')
            ->call('lookupTaxCode')
            ->assertHasErrors(['taxCode']);
    }

    public function test_sales_user_can_create_multi_service_quotation(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create([
            'name' => 'Công ty Năng lượng Sạch',
            'tax_code' => '0300000001',
        ]);
        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', $customer->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('formIssuedAt', '2026-07-04')
            ->set('formValidUntil', '2026-08-04')
            ->set('formWorkingSituation', 'Khách hàng đang xem phạm vi công việc')
            ->set('formOriginalAmount', '90000000')
            ->set('formCustomerCommission', '5000000')
            ->set('formCommissionTax', '500000')
            ->set('formContractValue', '95500000')
            ->set('serviceRows', [
                [
                    'service_type' => ServiceType::EsgConsulting->value,
                    'description' => 'Đánh giá hiện trạng ESG',
                    'quantity' => '1',
                    'unit_price' => '70000000',
                ],
                [
                    'service_type' => ServiceType::CbamConsulting->value,
                    'description' => 'Lập báo cáo CBAM',
                    'quantity' => '1',
                    'unit_price' => '30000000',
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $quotation = Quotation::query()->with('services')->firstOrFail();

        self::assertSame(QuotationStatus::Draft, $quotation->status);
        self::assertSame(100_000_000, $quotation->total_amount);
        self::assertSame(90_000_000, $quotation->original_amount);
        self::assertSame(5_000_000, $quotation->customer_commission);
        self::assertSame(500_000, $quotation->commission_tax);
        self::assertSame(95_500_000, $quotation->contract_value);
        self::assertSame('Khách hàng đang xem phạm vi công việc', $quotation->working_situation);
        self::assertCount(2, $quotation->services);
        self::assertStringStartsWith('BG-2026-', (string) $quotation->quotation_number);
    }

    public function test_quotation_form_accepts_browser_string_ids_from_select_fields(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::query()->where('username', 'superadmin')->firstOrFail();
        $owner = User::factory()->create();
        $owner->assignRole(RoleEnum::Sales->value);
        $customer = Customer::query()->create(['name' => 'Browser String Customer']);
        $this->actingAs($admin);

        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', (string) $customer->id)
            ->set('formOwnerId', (string) $owner->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('serviceRows', [[
                'service_type' => ServiceType::EsgConsulting->value,
                'description' => '',
                'quantity' => '1',
                'unit_price' => '10000000',
            ]])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quotations', [
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
        ]);
    }

    public function test_quotation_form_rejects_service_from_another_contract_type(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Khách hàng thử nghiệm']);
        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', $customer->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('serviceRows', [[
                'service_type' => ServiceType::SolarEnergyProject->value,
                'description' => '',
                'quantity' => '1',
                'unit_price' => '10000000',
            ]])
            ->call('save')
            ->assertHasErrors(['serviceRows.0.service_type']);
    }

    public function test_sales_can_convert_won_quotation_to_contract(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Chuyển đổi Xanh']);
        $quotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Won,
            'total_amount' => 120_000_000,
        ]);
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'quantity' => 1,
            'unit_price' => 120_000_000,
            'total_amount' => 120_000_000,
        ]);
        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->call('convertToContract', $quotation->id)
            ->assertHasNoErrors();

        $contract = Contract::query()->firstOrFail();
        self::assertSame($quotation->id, $contract->quotation_id);
        self::assertSame(120_000_000, $contract->value);
    }

    public function test_sales_can_convert_quotation_via_modal_form_with_custom_values(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Chuyển đổi Xanh']);
        $quotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Won,
            'total_amount' => 120_000_000,
        ]);
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'quantity' => 1,
            'unit_price' => 120_000_000,
            'total_amount' => 120_000_000,
        ]);
        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->call('openConvertModal', $quotation->id)
            ->assertSet('convertTitle', 'Hợp đồng - Công ty Chuyển đổi Xanh')
            ->assertSet('convertValue', '120000000')
            ->set('convertValue', '115000000') // Adjusting the price down
            ->set('convertOriginalAmount', '100000000')
            ->set('convertCustomerCommission', '10000000')
            ->set('convertCommissionTax', '1000000')
            ->set('convertContractNumber', 'HD-2026-999')
            ->set('convertNotes', 'Khách hàng đề nghị giảm giá trị hợp đồng')
            ->call('saveConversion')
            ->assertHasNoErrors();

        $contract = Contract::query()->firstOrFail();
        self::assertSame($quotation->id, $contract->quotation_id);
        self::assertSame(115_000_000, $contract->value); // Correct adjusted price
        self::assertSame(100_000_000, $contract->original_amount);
        self::assertSame(10_000_000, $contract->customer_commission);
        self::assertSame(1_000_000, $contract->commission_tax);
        self::assertSame('HD-2026-999', $contract->contract_number);
        self::assertSame('Khách hàng đề nghị giảm giá trị hợp đồng', $contract->notes);
    }

    public function test_conversion_form_creates_dynamic_payment_plan_and_initial_document(): void
    {
        Storage::fake('local');
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Hồ sơ Xanh']);
        $quotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Won,
            'total_amount' => 200_000_000,
            'contract_value' => 200_000_000,
        ]);
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'quantity' => 1,
            'unit_price' => 200_000_000,
            'total_amount' => 200_000_000,
        ]);
        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->call('openConvertModal', $quotation->id)
            ->set('convertPaymentRows', [
                [
                    'name' => 'Tạm ứng',
                    'percentage' => '30',
                    'amount' => '60000000',
                    'condition_type' => PaymentConditionType::AfterContractSigned->value,
                    'custom_condition' => '',
                    'expected_trigger_date' => '',
                    'payment_term_days' => '',
                    'payment_term_unit' => 'calendar_days',
                    'due_date' => '',
                    'notes' => '',
                ],
                [
                    'name' => 'Nghiệm thu',
                    'percentage' => '70',
                    'amount' => '140000000',
                    'condition_type' => PaymentConditionType::AfterAcceptance->value,
                    'custom_condition' => '',
                    'expected_trigger_date' => '',
                    'payment_term_days' => '10',
                    'payment_term_unit' => 'calendar_days',
                    'due_date' => '',
                    'notes' => '',
                ],
            ])
            ->call('addConvertDocumentRow')
            ->set('convertDocumentRows.0.type', DocumentType::Contract->value)
            ->set('convertDocumentRows.0.title', 'Hợp đồng bản ký')
            ->set('convertDocumentRows.0.file', UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf'))
            ->call('saveConversion')
            ->assertHasNoErrors();

        $contract = Contract::query()->firstOrFail();
        self::assertSame(2, $contract->paymentSchedules()->count());
        self::assertSame(200_000_000, (int) $contract->paymentSchedules()->sum('amount'));
        self::assertSame(1, $contract->documents()->count());
        Storage::disk('local')->assertExists($contract->documents()->firstOrFail()->file_path);
    }

    public function test_sales_can_delete_draft_quotation_but_cannot_delete_won_quotation(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Test Xóa']);

        $draftQuotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Draft,
            'total_amount' => 50_000_000,
        ]);

        $wonQuotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Won,
            'total_amount' => 100_000_000,
        ]);

        $this->actingAs($sales);

        // Delete draft should work
        Livewire::test(QuotationIndex::class)
            ->call('delete', $draftQuotation->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('quotations', [
            'id' => $draftQuotation->id,
        ]);

        // Delete won should fail authorization
        Livewire::test(QuotationIndex::class)
            ->call('delete', $wonQuotation->id)
            ->assertForbidden();
    }

    public function test_sales_can_upload_and_download_and_delete_quotation_file(): void
    {
        Storage::fake('local');

        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Test File']);

        $this->actingAs($sales);

        $file = UploadedFile::fake()->create('quotation_offer.pdf', 500, 'application/pdf');

        // Create quotation with file
        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', $customer->id)
            ->set('formOwnerId', $sales->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('formIssuedAt', now()->toDateString())
            ->set('formValidUntil', now()->addDays(30)->toDateString())
            ->set('serviceRows', [
                [
                    'service_type' => ServiceType::EsgConsulting->value,
                    'description' => 'Test Service',
                    'quantity' => 1,
                    'unit_price' => 5_000_000,
                ],
            ])
            ->set('formFile', $file)
            ->call('save')
            ->assertHasNoErrors();

        $quotation = Quotation::query()->latest('id')->firstOrFail();
        $this->assertNotNull($quotation->file_path);
        Storage::disk('local')->assertExists($quotation->file_path);

        $viewResponse = $this->get(route('quotations.file.view', $quotation));
        $viewResponse->assertOk();
        $this->assertStringStartsWith('inline;', (string) $viewResponse->headers->get('content-disposition'));

        Livewire::test(QuotationIndex::class)
            ->call('openDetail', $quotation->id)
            ->assertSet('viewingId', $quotation->id)
            ->assertDispatched('quotation-detail:show')
            ->assertSee('Chi tiết báo giá')
            ->assertSee($quotation->quotation_number);

        // Download file should work
        Livewire::test(QuotationIndex::class)
            ->call('downloadFile', $quotation->id)
            ->assertFileDownloaded();

        $oldFilePath = $quotation->file_path;

        // Edit and clear file
        Livewire::test(QuotationIndex::class)
            ->call('openEdit', $quotation->id)
            ->assertSet('existingFilePath', $quotation->file_path)
            ->call('deleteFile')
            ->assertSet('existingFilePath', null);

        $quotation->refresh();
        $this->assertNull($quotation->file_path);
        Storage::disk('local')->assertMissing($oldFilePath);
    }

    public function test_can_upload_and_manage_multiple_files_for_quotation(): void
    {
        Storage::fake('local');
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Nhiều File']);

        $file1 = UploadedFile::fake()->create('bao_gia_1.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->create('bang_tinh_2.xlsx', 150, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($sales);

        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', $customer->id)
            ->set('formOwnerId', $sales->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('formIssuedAt', now()->toDateString())
            ->set('serviceRows', [
                [
                    'service_type' => ServiceType::EsgConsulting->value,
                    'description' => 'Test Service Multi File',
                    'quantity' => 1,
                    'unit_price' => 10_000_000,
                ],
            ])
            ->set('formFiles', [$file1, $file2])
            ->call('save')
            ->assertHasNoErrors();

        $quotation = Quotation::query()->latest('id')->firstOrFail();
        $this->assertCount(2, $quotation->files);

        $savedFile1 = $quotation->files->first();
        Storage::disk('local')->assertExists($savedFile1->file_path);

        $viewResponse = $this->get(route('quotations.attachments.view', $savedFile1));
        $viewResponse->assertOk();

        // Test deleting one of the existing files
        Livewire::test(QuotationIndex::class)
            ->call('openEdit', $quotation->id)
            ->assertCount('existingFiles', 2)
            ->call('deleteExistingFile', $savedFile1->id)
            ->assertCount('existingFiles', 1);

        $this->assertDatabaseMissing('quotation_files', ['id' => $savedFile1->id]);
        Storage::disk('local')->assertMissing($savedFile1->file_path);
    }

    public function test_can_create_and_convert_quotation_over_2_billion_vnd(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Test Trên 2 Tỷ']);

        $this->actingAs($sales);

        // Create quotation with 3 billion VND value
        Livewire::test(QuotationIndex::class)
            ->set('formCustomerId', $customer->id)
            ->set('formOwnerId', $sales->id)
            ->set('formContractType', ContractType::Consulting->value)
            ->set('formIssuedAt', now()->toDateString())
            ->set('formValidUntil', now()->addDays(30)->toDateString())
            ->set('serviceRows', [
                [
                    'service_type' => ServiceType::EsgConsulting->value,
                    'description' => 'Test Service 3 Tỷ',
                    'quantity' => 1,
                    'unit_price' => 3_000_000_000,
                ],
            ])
            ->set('formOriginalAmount', 3_000_000_000)
            ->set('formContractValue', 3_000_000_000)
            ->call('save')
            ->assertHasNoErrors();

        $quotation = Quotation::query()->latest('id')->firstOrFail();
        $this->assertEquals(3_000_000_000, $quotation->contract_value);

        // Now transition status to Won
        $quotation->update(['status' => QuotationStatus::Won]);

        // Convert quotation to contract
        Livewire::test(QuotationIndex::class)
            ->call('openConvertModal', $quotation->id)
            ->set('convertTitle', 'Hợp đồng 3 Tỷ')
            ->set('convertValue', 3_000_000_000)
            ->set('convertOriginalAmount', 3_000_000_000)
            ->set('convertCustomerCommission', 0)
            ->set('convertCommissionTax', 0)
            ->set('convertPaymentRows', [
                [
                    'name' => 'Đợt 1',
                    'percentage' => 100,
                    'amount' => 3_000_000_000,
                    'condition_type' => PaymentConditionType::AfterContractSigned->value,
                    'custom_condition' => '',
                    'expected_trigger_date' => '',
                    'payment_term_days' => 15,
                    'payment_term_unit' => PaymentTermUnit::CalendarDays->value,
                    'due_date' => '',
                    'notes' => '',
                ],
            ])
            ->call('saveConversion')
            ->assertHasNoErrors();

        $contract = Contract::query()->latest('id')->firstOrFail();
        $this->assertEquals(3_000_000_000, $contract->value);
    }

    public function test_can_edit_quotation_status(): void
    {
        $sales = $this->salesUser();
        $customer = Customer::query()->create(['name' => 'Công ty Test Sửa Trạng Thái BG']);
        $this->actingAs($sales);

        $quotation = Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $sales->id,
            'contract_type' => ContractType::Consulting->value,
            'issued_at' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'status' => QuotationStatus::Draft->value,
            'total_amount' => 100_000_000,
            'original_amount' => 100_000_000,
            'contract_value' => 100_000_000,
        ]);
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting->value,
            'description' => 'Tư vấn ESG',
            'quantity' => 1,
            'unit_price' => 100_000_000,
            'total_amount' => 100_000_000,
            'sort_order' => 0,
        ]);

        Livewire::test(QuotationIndex::class)
            ->call('openEdit', $quotation->id)
            ->assertSet('formStatus', QuotationStatus::Draft->value)
            ->set('formStatus', QuotationStatus::Sent->value)
            ->call('save')
            ->assertHasNoErrors();

        self::assertSame(QuotationStatus::Sent, $quotation->refresh()->status);
    }

    private function salesUser(): User
    {
        $this->seed(PermissionSeeder::class);

        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);

        return $sales;
    }
}
