<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\RoleEnum;
use App\Livewire\Contracts\ContractWorkflowPanel;
use App\Models\Contract;
use App\Models\ContractWorkflowStep;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\ContractWorkflowUpdated;
use App\Services\Contracts\ContractProgressService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

final class ContractProgressParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        Storage::fake('local');
        Storage::fake('public');
        Notification::fake();
    }

    public function test_sequential_workflow_persists_status_and_notes_for_all_greeco_types(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        foreach (ContractType::cases() as $type) {
            $contract = $this->contract($actor, $type);
            $this->assign($contract, $actor);
            $this->actingAs($actor);
            foreach (ContractWorkflowStep::STEP_KEYS as $step) {
                app(ContractProgressService::class)->complete($contract, $step, 'Ghi chú <script>alert(1)</script> '.$step, [], $actor);
                $this->assertSame($step, $contract->fresh()->workflow_status);
            }
            Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
                ->assertSee('Ghi chú &lt;script&gt;alert(1)&lt;/script&gt; finished', false)
                ->assertDontSee('<script>alert(1)</script>', false);
            $this->assertSame(6, $contract->workflowSteps()->count());
        }
    }

    public function test_rejects_invalid_skipped_and_repeated_steps_on_server(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $this->assign($contract, $actor);
        $this->actingAs($actor);
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->set('activeStep', 'finished')->call('completeStep')->assertHasErrors('activeStep');
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->set('activeStep', 'invalid')->call('completeStep')->assertHasErrors('activeStep');
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->call('openStep', 'receiving')->call('completeStep')->assertHasNoErrors()
            ->set('activeStep', 'receiving')->call('completeStep')->assertHasErrors('activeStep');
        $this->assertSame(1, $contract->workflowSteps()->count());
    }

    public function test_supplement_appends_files_without_reopening_progress_or_notifying(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $this->assign($contract, $actor);
        $service = app(ContractProgressService::class);
        $service->complete($contract, 'receiving', 'Ghi chú cũ', [UploadedFile::fake()->create('original.pdf', 1, 'application/pdf')], $actor);
        $service->complete($contract, 'survey', '', [], $actor);
        $beforeHistory = $contract->workflowSteps()->count();
        Notification::fake();
        $this->actingAs($actor);

        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->call('openFileSupplement', 'receiving')
            ->set('uploadFiles', [UploadedFile::fake()->create('supplement.pdf', 1, 'application/pdf')])
            ->call('saveSupplementFiles')->assertHasNoErrors();

        $this->assertSame('survey', $contract->fresh()->workflow_status);
        $this->assertSame($beforeHistory, $contract->workflowSteps()->count());
        $this->assertSame(2, $contract->milestoneFiles()->count());
        $this->assertSame(['original.pdf', 'supplement.pdf'], $contract->milestoneFiles()->orderBy('id')->pluck('original_name')->all());
        Notification::assertNothingSent();
    }

    public function test_rechecks_assignment_when_saving_and_only_supplements_completed_steps(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $this->actingAs($actor);
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->set('activeStep', 'receiving')->call('completeStep')->assertForbidden();
        $this->assign($contract, $actor);
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])
            ->call('openFileSupplement', 'receiving')->assertStatus(422);
        app(ContractProgressService::class)->complete($contract, 'receiving', '', [], $actor);
        $component = Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])->call('openFileSupplement', 'receiving');
        $contract->assignments()->delete();
        $component->set('uploadFiles', [UploadedFile::fake()->create('extra.pdf', 1, 'application/pdf')])
            ->call('saveSupplementFiles')->assertForbidden();
        $this->assertSame(0, $contract->milestoneFiles()->count());
    }

    public function test_upload_validation_accepts_over_50mb_and_rejects_over_200mb_or_invalid_types(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $this->assign($contract, $actor);
        $service = app(ContractProgressService::class);
        $service->complete($contract, 'receiving', '', [UploadedFile::fake()->create('large.pdf', 51201, 'application/pdf')], $actor);
        $this->assertSame(1, $contract->milestoneFiles()->count());
        $this->assertContains('max:204800', config('livewire.temporary_file_upload.rules'));
        $this->actingAs($actor);
        foreach ([UploadedFile::fake()->create('oversized.pdf', 204801, 'application/pdf'),
            UploadedFile::fake()->create('script.exe', 1, 'application/octet-stream')] as $file) {
            try {
                $service->complete($contract, 'survey', '', [$file], $actor);
                $this->fail('File vượt giới hạn hoặc sai định dạng phải bị từ chối.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('uploadFiles.0', $exception->errors());
            }
        }
        $this->assertSame('receiving', $contract->fresh()->workflow_status);
    }

    public function test_accountants_receive_only_completion_and_it_does_not_receive_progress(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $owner = $this->user(RoleEnum::Sales);
        $director = $this->user(RoleEnum::Director);
        $accountant = $this->user(RoleEnum::Accountant);
        $it = $this->user(RoleEnum::IT);
        $contract = $this->contract($owner);
        $this->assign($contract, $actor);
        $service = app(ContractProgressService::class);
        $service->complete($contract, 'receiving', '', [], $actor);
        Notification::assertSentTo([$owner, $director], ContractWorkflowUpdated::class);
        Notification::assertNotSentTo([$it, $accountant, $actor], ContractWorkflowUpdated::class);
        foreach (array_slice(ContractWorkflowStep::STEP_KEYS, 1) as $step) {
            $service->complete($contract, $step, '', [], $actor);
        }
        Notification::assertSentTo($accountant, ContractWorkflowUpdated::class, fn ($notification) => $notification->stepName === 'finished');
        $this->assertCount(1, Notification::sent($accountant, ContractWorkflowUpdated::class));
        Notification::assertNotSentTo($it, ContractWorkflowUpdated::class);
    }

    public function test_download_and_delete_check_contract_file_ownership_and_support_legacy_disk(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $other = $this->contract($actor);
        $this->assign($contract, $actor);
        $this->assign($other, $actor);
        app(ContractProgressService::class)->complete($contract, 'receiving', '', [UploadedFile::fake()->create('private.pdf', 1, 'application/pdf')], $actor);
        $file = $contract->milestoneFiles()->firstOrFail();
        $this->actingAs($actor)->get(route('contracts.milestone-files.download', [$contract, $file]))->assertOk()->assertDownload('private.pdf');
        $this->get(route('contracts.milestone-files.download', [$other, $file]))->assertNotFound();
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $other->id])->call('deleteFile', $file->id)->assertNotFound();
        Storage::disk('local')->assertExists($file->file_path);
        Storage::disk('public')->put('contract-files/legacy.pdf', 'legacy');
        $legacy = $contract->milestoneFiles()->create(['milestone' => 'receiving', 'original_name' => 'legacy.pdf', 'file_path' => 'contract-files/legacy.pdf', 'uploader_id' => $actor->id]);
        $this->get(route('contracts.milestone-files.download', [$contract, $legacy]))->assertOk();
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])->call('deleteFile', $file->id)->assertHasNoErrors();
        Storage::disk('local')->assertMissing($file->file_path);
        $this->assertModelMissing($file);
    }

    public function test_contract_id_is_locked_against_livewire_tampering(): void
    {
        $actor = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($actor);
        $other = $this->contract($actor);
        $this->actingAs($actor);
        $this->expectException(CannotUpdateLockedPropertyException::class);
        Livewire::test(ContractWorkflowPanel::class, ['contractId' => $contract->id])->set('contractId', $other->id);
    }

    private function user(RoleEnum $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user;
    }

    private function contract(User $owner, ContractType $type = ContractType::Consulting): Contract
    {
        return Contract::create(['customer_id' => Customer::create(['name' => 'Khách hàng'])->id, 'owner_id' => $owner->id,
            'title' => 'Hợp đồng '.$type->value, 'type' => $type, 'status' => ContractStatus::Draft, 'value' => 1000000]);
    }

    private function assign(Contract $contract, User $user): void
    {
        $contract->assignments()->create(['user_id' => $user->id, 'assigned_by' => $contract->owner_id]);
    }
}
