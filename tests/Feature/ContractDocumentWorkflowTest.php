<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\RoleEnum;
use App\Livewire\Contracts\ContractIndex;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\Customer;
use App\Models\User;
use App\Services\Contracts\ContractDocumentService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class ContractDocumentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_keeps_versions_and_archives_approved_predecessor(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $accountant = User::factory()->create();
        $accountant->assignRole(RoleEnum::Accountant->value);
        $contract = $this->contract($sales);
        $service = app(ContractDocumentService::class);

        $first = $service->create($contract, [
            'type' => DocumentType::PaymentRequest,
            'title' => 'Đề nghị thanh toán đợt 1',
            'file_path' => 'contracts/test/request-v1.pdf',
        ], $sales);
        $service->submit($first, $sales);
        $service->startReview($first->refresh(), $accountant);
        $service->review($first->refresh(), DocumentStatus::RevisionRequired, $accountant, 'Bổ sung chữ ký.');

        $second = $service->create($contract, [
            'type' => DocumentType::PaymentRequest,
            'title' => 'Đề nghị thanh toán đợt 1',
            'file_path' => 'contracts/test/request-v2.pdf',
        ], $sales, $first->refresh());
        $service->submit($second, $sales);
        $service->review($second->refresh(), DocumentStatus::Approved, $accountant);

        self::assertSame(1, $first->version);
        self::assertSame(2, $second->version);
        self::assertSame($first->id, $second->supersedes_id);
        self::assertSame(DocumentStatus::Archived, $first->refresh()->status);
        self::assertSame(DocumentStatus::Approved, $second->refresh()->status);
    }

    public function test_authorized_user_can_download_private_contract_document(): void
    {
        Storage::fake('local');
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $contract = $this->contract($sales);
        Storage::disk('local')->put('contracts/test/document.pdf', 'private-content');
        $document = ContractDocument::query()->create([
            'contract_id' => $contract->id,
            'type' => DocumentType::Contract,
            'status' => DocumentStatus::Draft,
            'title' => 'Hợp đồng đã ký',
            'file_path' => 'contracts/test/document.pdf',
            'submitted_by' => $sales->id,
        ]);

        $response = $this->actingAs($sales)->get(route('contracts.documents.download', [$contract, $document]));

        $response->assertOk();
        $response->assertDownload('Hop dong da ky.pdf');
    }

    public function test_authorized_user_can_open_contract_detail_modal(): void
    {
        $this->seed(PermissionSeeder::class);
        $sales = User::factory()->create();
        $sales->assignRole(RoleEnum::Sales->value);
        $contract = $this->contract($sales);

        $this->actingAs($sales);

        Livewire::test(ContractIndex::class)
            ->call('openDetail', $contract->id)
            ->assertSet('viewingId', $contract->id)
            ->assertDispatched('contract-detail:show')
            ->assertSee('Chi tiết hợp đồng')
            ->assertSee($contract->title);
    }

    private function contract(User $owner): Contract
    {
        $customer = Customer::query()->create(['name' => 'Công ty Xanh']);

        return Contract::query()->create([
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
            'department_id' => $owner->department_id,
            'type' => ContractType::Consulting,
            'status' => ContractStatus::Active,
            'title' => 'Hợp đồng ESG',
            'value' => 100_000_000,
            'signed_at' => '2026-07-01',
        ]);
    }
}
