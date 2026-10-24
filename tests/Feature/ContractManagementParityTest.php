<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentConditionType;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\ServiceType;
use App\Livewire\Contracts\ContractAttachments;
use App\Livewire\Contracts\ContractForm;
use App\Livewire\Contracts\ContractIndex;
use App\Livewire\Contracts\ContractShow;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\ContractAssigned;
use App\Notifications\ContractProgressNoted;
use App\Services\Contracts\ContractAssignmentService;
use App\Services\Contracts\ContractListing;
use App\Services\Contracts\ContractNoteService;
use App\Services\Contracts\ContractService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

final class ContractManagementParityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        Notification::fake();
        Storage::fake('local');
    }

    public function test_sales_only_sees_related_contracts_and_view_all_can_be_granted_directly(): void
    {
        $actor = $this->user(RoleEnum::Sales);
        $other = $this->user(RoleEnum::Sales);
        $own = $this->contract($actor, 'Hợp đồng của tôi');
        $private = $this->contract($other, 'Hợp đồng riêng người khác');
        $this->actingAs($actor);
        Livewire::test(ContractIndex::class)->assertSee('Hợp đồng của tôi')->assertDontSee('Hợp đồng riêng người khác');
        $this->assertSame(1, app(ContractService::class)->summary()['total']);
        $this->get(route('contracts.show', $private))->assertForbidden();
        $this->get(route('contracts.edit', $private->id))->assertForbidden();
        $actor->givePermissionTo(PermissionEnum::ContractViewAll->value);
        $this->get(route('contracts.show', $private))->assertOk();
        $this->assertSame(2, app(ContractService::class)->summary()['total']);
        $this->assertTrue($actor->can('view', $own));
    }

    public function test_filters_search_and_sort_apply_to_the_same_visible_query(): void
    {
        $actor = $this->user(RoleEnum::Sales);
        $first = $this->contract($actor, 'Hợp đồng A');
        $first->update(['province' => 'Long An', 'signed_at' => '2026-10-24', 'submitted_at' => '2026-10-23', 'voucher_status' => 'invoiced', 'report_number' => 'BC-002']);
        $second = $this->contract($actor, 'Hợp đồng B');
        $second->update(['province' => 'TP HCM', 'signed_at' => '2026-09-01', 'report_number' => 'BC-001']);
        $this->actingAs($actor);
        $listing = app(ContractListing::class);
        $this->assertSame([$first->id], $listing->query('', '', '', ['province' => 'Long An', 'signed_from' => '2026-10-01', 'voucher_status' => 'invoiced'])->pluck('id')->all());
        $this->assertSame([$first->id], $listing->query('BC-002')->pluck('id')->all());
        $this->assertSame([$second->id, $first->id], $listing->ordered($listing->query(), 'report_number', 'asc')->pluck('id')->all());
        $this->assertCount(2, $listing->query('', '', '', ['signed_from' => 'invalid'])->get());
        Livewire::test(ContractIndex::class)->set('filter.province', 'Long An')->assertSee('Hợp đồng A')->assertDontSee('Hợp đồng B');
    }

    public function test_export_uses_filters_and_cannot_export_unrelated_contracts(): void
    {
        $actor = $this->user(RoleEnum::Sales);
        $other = $this->user(RoleEnum::Sales);
        $this->contract($actor, 'Bản được xuất')->update(['province' => 'Long An']);
        $this->contract($actor, 'Bản không khớp')->update(['province' => 'Hà Nội']);
        $this->contract($other, 'Bí mật người khác')->update(['province' => 'Long An']);
        $this->actingAs($actor);
        $component = Livewire::test(ContractIndex::class)->set('filter.province', 'Long An')->call('exportExcel')->assertFileDownloaded();
        $export = base64_decode($component->effects['download']['content']);
        $this->assertStringContainsString('Bản được xuất', $export);
        $this->assertStringNotContainsString('Bản không khớp', $export);
        $this->assertStringNotContainsString('Bí mật người khác', $export);
    }

    public function test_assignment_sync_preserves_history_and_only_notifies_new_assignees(): void
    {
        $owner = $this->user(RoleEnum::Sales);
        $assignee = $this->user(RoleEnum::Consultant);
        $other = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($owner, 'Phân công');
        $service = app(ContractAssignmentService::class);
        $service->sync($contract, [$assignee->id], 'Đối tác A', '2026-11-01', $owner);
        $assignment = $contract->assignments()->where('user_id', $assignee->id)->firstOrFail();
        Notification::assertSentTo($assignee, ContractAssigned::class);
        Notification::fake();
        $service->sync($contract, [$assignee->id, $other->id], 'Đối tác B', '2026-11-02', $owner);
        $this->assertSame($assignment->id, $contract->assignments()->where('user_id', $assignee->id)->first()->id);
        $this->assertSame('2026-11-02', $assignment->fresh()->deadline->toDateString());
        Notification::assertNotSentTo($assignee, ContractAssigned::class);
        Notification::assertSentTo($other, ContractAssigned::class);
        $this->assertSame('Đối tác B', $contract->assignments()->whereNull('user_id')->first()->external_assignee);
        $service->sync($contract, [$other->id], '', '', $owner);
        $this->assertSame([$other->id], $contract->assignments()->pluck('user_id')->all());
    }

    public function test_assigned_consultant_can_note_and_unassigned_user_cannot_read_or_note(): void
    {
        $owner = $this->user(RoleEnum::Sales);
        $assigned = $this->user(RoleEnum::Consultant);
        $stranger = $this->user(RoleEnum::Consultant);
        $contract = $this->contract($owner, 'Trao đổi');
        app(ContractAssignmentService::class)->sync($contract, [$assigned->id], '', '', $owner);
        Notification::fake();
        $this->actingAs($assigned);
        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->set('progressNote', 'Ghi chú <script>alert(1)</script>')->call('addProgressNote')->assertHasNoErrors();
        $this->assertSame($assigned->id, $contract->progressNotes()->first()->created_by);
        Notification::assertSentTo($owner, ContractProgressNoted::class);
        $this->actingAs($stranger)->get(route('contracts.show', $contract))->assertForbidden();
        $this->expectException(AuthorizationException::class);
        app(ContractNoteService::class)->add($contract, 'Không có quyền', $stranger);
    }

    public function test_general_attachments_keep_workflow_unchanged_and_check_ownership_on_delete(): void
    {
        $owner = $this->user(RoleEnum::Sales);
        $contract = $this->contract($owner, 'Hồ sơ');
        $other = $this->contract($owner, 'Hồ sơ khác');
        $this->actingAs($owner);
        Livewire::test(ContractAttachments::class, ['contractId' => $contract->id])
            ->set('files', [UploadedFile::fake()->create('hop-dong.pdf', 1, 'application/pdf')])->call('upload')->assertHasNoErrors();
        $file = $contract->milestoneFiles()->firstOrFail();
        $this->assertSame('contract', $file->milestone);
        $this->assertNull($contract->fresh()->workflow_status);
        $this->assertSame(0, $contract->workflowSteps()->count());
        Livewire::test(ContractAttachments::class, ['contractId' => $other->id])->call('deleteFile', $file->id)->assertNotFound();
        Livewire::test(ContractAttachments::class, ['contractId' => $contract->id])->call('deleteFile', $file->id)->assertHasNoErrors();
        Storage::disk('local')->assertMissing($file->file_path);
    }

    public function test_creating_with_assignment_rolls_back_if_assignment_is_invalid(): void
    {
        $actor = $this->user(RoleEnum::Sales);
        $customer = Customer::create(['name' => 'Khách hàng']);
        $this->actingAs($actor);
        Livewire::test(ContractForm::class)->set('form.title', 'Không được lưu một nửa')->set('form.customer_id', $customer->id)
            ->set('serviceRows', [['service_type' => ServiceType::EsgConsulting->value, 'description' => '', 'amount' => 0]])
            ->set('createAssignUserIds', [999999])->call('save')->assertHasErrors('assignUserIds.0');
        $this->assertDatabaseMissing('contracts', ['title' => 'Không được lưu một nửa']);
    }

    private function user(RoleEnum $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);

        return $user;
    }

    public function test_inline_report_and_status_updates_are_validated_and_scoped(): void
    {
        $owner = $this->user(RoleEnum::Sales);
        $other = $this->user(RoleEnum::Sales);
        $contract = $this->contract($owner, 'Cập nhật nhanh');
        $private = $this->contract($other, 'Không thuộc quyền');
        $this->actingAs($owner);
        Livewire::test(ContractIndex::class)->call('updateInlineReportNumber', $contract->id, 'BC-2026')
            ->call('updateStatus', $contract->id, ContractStatus::InternalReview->value)->assertHasNoErrors();
        $this->assertSame('BC-2026', $contract->fresh()->report_number);
        $this->assertSame(ContractStatus::InternalReview, $contract->fresh()->status);
        Livewire::test(ContractIndex::class)->call('updateStatus', $contract->id, 'forged')->assertHasErrors('contractStatus');
        Livewire::test(ContractIndex::class)->call('updateInlineReportNumber', $private->id, 'Trái phép')->assertForbidden();
    }

    public function test_internal_review_and_director_signature_use_separate_approval_permission(): void
    {
        $owner = $this->user(RoleEnum::Sales);
        $director = $this->user(RoleEnum::Director);
        $contract = $this->contract($owner, 'Trình ký lãnh đạo');
        $contract->services()->create(['service_type' => ServiceType::EsgConsulting, 'amount' => 1000000]);
        $contract->paymentSchedules()->create(['installment_number' => 1, 'name' => 'Thanh toán', 'amount' => 1000000,
            'percentage' => 100, 'condition_type' => PaymentConditionType::AfterContractSigned]);
        $this->actingAs($owner);
        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->call('transitionContract', ContractStatus::InternalReview->value)
            ->call('transitionContract', ContractStatus::WaitingDirectorSignature->value)->assertHasNoErrors();
        $this->assertSame(ContractStatus::WaitingDirectorSignature, $contract->fresh()->status);
        $this->actingAs($director);
        Livewire::test(ContractShow::class, ['contract' => $contract])
            ->call('transitionContract', ContractStatus::WaitingCustomerSignature->value)->assertHasNoErrors();
        $this->assertSame(ContractStatus::WaitingCustomerSignature, $contract->fresh()->status);
    }

    private function contract(User $owner, string $title): Contract
    {
        return Contract::create(['customer_id' => Customer::create(['name' => 'Khách hàng'])->id, 'owner_id' => $owner->id,
            'title' => $title, 'type' => ContractType::Consulting, 'status' => ContractStatus::Draft, 'value' => 1000000]);
    }
}
