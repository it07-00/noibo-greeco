<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\QuotationStatus;
use App\Enums\ServiceType;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;
use App\Services\Quotations\QuotationToContractService;
use App\Services\Quotations\QuotationVersionService;
use App\Services\Quotations\QuotationWorkflowService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class QuotationContractWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_send_quotation_without_approval_state(): void
    {
        $quotation = $this->createQuotation();
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'description' => 'Tư vấn ESG',
            'quantity' => 1,
            'unit_price' => 100_000_000,
            'total_amount' => 100_000_000,
        ]);

        $quotation = app(QuotationWorkflowService::class)
            ->transition($quotation, QuotationStatus::Sent);

        self::assertSame(QuotationStatus::Sent, $quotation->status);
        self::assertNotNull($quotation->sent_at);
    }

    public function test_quotation_cannot_be_sent_without_services(): void
    {
        $quotation = $this->createQuotation();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ít nhất một dịch vụ');

        app(QuotationWorkflowService::class)
            ->transition($quotation, QuotationStatus::Sent);
    }

    public function test_service_must_match_quotation_contract_type(): void
    {
        $quotation = $this->createQuotation();
        $quotation->services()->create([
            'service_type' => ServiceType::SolarEnergyProject,
            'quantity' => 1,
            'unit_price' => 100_000_000,
            'total_amount' => 100_000_000,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('không thuộc loại hợp đồng');

        app(QuotationWorkflowService::class)
            ->transition($quotation, QuotationStatus::Sent);
    }

    public function test_won_quotation_converts_to_one_contract_with_services(): void
    {
        $quotation = $this->createQuotation();
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'description' => 'Tư vấn ESG',
            'quantity' => 1,
            'unit_price' => 100_000_000,
            'total_amount' => 100_000_000,
        ]);

        $workflow = app(QuotationWorkflowService::class);
        $quotation = $workflow->transition($quotation, QuotationStatus::Sent);
        $quotation = $workflow->transition($quotation, QuotationStatus::Won);

        $contract = app(QuotationToContractService::class)
            ->convert($quotation, 'HD-2026-001');

        self::assertSame(ContractStatus::Draft, $contract->status);
        self::assertSame(ContractType::Consulting, $contract->type);
        self::assertSame(100_000_000, $contract->value);
        self::assertSame('HD-2026-001', $contract->contract_number);
        self::assertCount(1, $contract->services);
        self::assertSame(ServiceType::EsgConsulting, $contract->services->first()->service_type);
        self::assertNotNull($quotation->refresh()->converted_at);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('đã được chuyển');

        app(QuotationToContractService::class)->convert($quotation);
    }

    public function test_quotation_versions_preserve_sent_content(): void
    {
        $quotation = $this->createQuotation();
        $quotation->services()->create([
            'service_type' => ServiceType::EsgConsulting,
            'quantity' => 1,
            'unit_price' => 100_000_000,
            'total_amount' => 100_000_000,
        ]);
        $actor = User::factory()->create();
        $service = app(QuotationVersionService::class);

        $versionOne = $service->capture($quotation, $actor, 'Bản gửi lần đầu');
        $quotation->update(['total_amount' => 120_000_000]);
        $versionTwo = $service->capture($quotation, $actor, 'Điều chỉnh giá');

        self::assertSame(1, $versionOne->version);
        self::assertSame(100_000_000, $versionOne->snapshot['quotation']['total_amount']);
        self::assertSame(2, $versionTwo->version);
        self::assertSame(120_000_000, $versionTwo->snapshot['quotation']['total_amount']);
    }

    private function createQuotation(): Quotation
    {
        $customer = Customer::query()->create([
            'name' => 'Công ty TNHH Greeco Test',
            'tax_code' => fake()->unique()->numerify('##########'),
        ]);

        return Quotation::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => User::factory()->create()->id,
            'quotation_number' => fake()->unique()->numerify('BG-2026-####'),
            'contract_type' => ContractType::Consulting,
            'status' => QuotationStatus::Draft,
            'total_amount' => 100_000_000,
        ]);
    }
}
