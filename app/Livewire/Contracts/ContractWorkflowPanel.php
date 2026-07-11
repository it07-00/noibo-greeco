<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Models\ContractAssignment;
use App\Models\ContractMilestoneFile;
use App\Models\ContractWorkflowStep;
use App\Models\User;
use App\Notifications\ContractWorkflowUpdated;
use Livewire\Component;
use Livewire\WithFileUploads;

final class ContractWorkflowPanel extends Component
{
    use WithFileUploads;

    public int $contractId;
    public array $uploadFiles = [];
    public string $comment = '';
    public ?string $activeStep = null; // step being confirmed

    public function mount(int $contractId): void
    {
        $this->contractId = $contractId;
    }

    public function canEdit(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Super Admin, Viện Trưởng, and IT can always edit
        if ($user->hasRole(\App\Enums\RoleEnum::SuperAdmin->value) || 
            $user->hasRole(\App\Enums\RoleEnum::Director->value) || 
            $user->hasRole(\App\Enums\RoleEnum::IT->value)) {
            return true;
        }

        // Assigned users can edit
        return ContractAssignment::where('assignable_type', Contract::class)
            ->where('assignable_id', $this->contractId)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function openStep(string $step): void
    {
        if (! $this->canEdit()) {
            return;
        }
        $this->activeStep = $step;
        $this->uploadFiles = [];
        $this->comment = '';
    }

    public function completeStep(): void
    {
        if (! $this->canEdit()) {
            $this->dispatch('swal:toast', ['type' => 'error', 'message' => 'Bạn không có quyền thực hiện thao tác này.']);
            return;
        }

        $fileRequired = in_array($this->activeStep, ['processing', 'finished'], true);

        $rules = [
            'uploadFiles' => ($fileRequired ? 'required|array|max:10|min:1' : 'nullable|array|max:10'),
            'uploadFiles.*' => 'file|max:204800|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'comment' => 'nullable|string|max:1000',
        ];

        $messages = [
            'uploadFiles.required' => 'Vui lòng đính kèm ít nhất 1 file trước khi xác nhận bước này.',
            'uploadFiles.min' => 'Vui lòng đính kèm ít nhất 1 file trước khi xác nhận bước này.',
            'uploadFiles.*.max' => 'Mỗi file không được vượt quá 200MB.',
            'uploadFiles.*.mimes' => 'Chỉ chấp nhận file PDF, Word, Excel, JPG, PNG.',
        ];

        $this->validate($rules, $messages);

        $uploadDisk = config('filesystems.upload_disk', 'public');

        if (! empty($this->uploadFiles)) {
            foreach ($this->uploadFiles as $file) {
                $path = $file->storePublicly(
                    'contract-files/contract/' . $this->activeStep,
                    $uploadDisk
                );

                ContractMilestoneFile::create([
                    'contract_type' => Contract::class,
                    'contract_id' => $this->contractId,
                    'milestone' => $this->activeStep,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'uploader_id' => auth()->id(),
                ]);
            }
        }

        ContractWorkflowStep::create([
            'contract_type' => Contract::class,
            'contract_id' => $this->contractId,
            'user_id' => auth()->id(),
            'step_name' => $this->activeStep,
            'action' => 'complete',
            'comment' => $this->comment ?: null,
        ]);

        $contract = Contract::findOrFail($this->contractId);
        $contract->update([
            'workflow_status' => $this->activeStep,
        ]);

        $stepLabel = ContractWorkflowStep::STEPS[$this->activeStep] ?? $this->activeStep;
        $completedStep = $this->activeStep;

        $this->activeStep = null;
        $this->uploadFiles = [];
        $this->comment = '';

        $this->dispatch('swal:toast', [
            'type' => 'success',
            'message' => 'Đã hoàn thành bước: ' . $stepLabel,
        ]);

        $contractLabel = $contract->contract_number ?: $contract->title;

        $recipients = User::role([\App\Enums\RoleEnum::Director->value, \App\Enums\RoleEnum::IT->value])->get();

        $assignmentUserIds = ContractAssignment::where('assignable_type', Contract::class)
            ->where('assignable_id', $this->contractId)
            ->get(['user_id', 'assigned_by'])
            ->flatMap(fn($assignment) => [(int) $assignment->user_id, (int) $assignment->assigned_by])
            ->filter()
            ->unique()
            ->values();

        if ($assignmentUserIds->isNotEmpty()) {
            $recipients = $recipients->merge(User::whereIn('id', $assignmentUserIds)->get());
        }

        if ($contract->owner_id && $contract->owner_id !== auth()->id()) {
            $owner = User::find($contract->owner_id);
            if ($owner) {
                $recipients->push($owner);
            }
        }

        foreach ($recipients->unique('id') as $recipient) {
            if ($recipient->id !== auth()->id()) {
                $recipient->notify(new ContractWorkflowUpdated(
                    $this->contractId,
                    $contractLabel,
                    $completedStep,
                    $stepLabel,
                    auth()->user()->name
                ));
            }
        }

        $this->dispatch('contract-workflow:updated');
    }

    public function cancelStep(): void
    {
        $this->activeStep = null;
        $this->uploadFiles = [];
        $this->comment = '';
    }

    public function render()
    {
        $contract = Contract::find($this->contractId);
        $currentStatus = $contract?->workflow_status;

        $completedSteps = ContractWorkflowStep::where('contract_type', Contract::class)
            ->where('contract_id', $this->contractId)
            ->pluck('step_name')
            ->toArray();

        $filesByStep = ContractMilestoneFile::where('contract_type', Contract::class)
            ->where('contract_id', $this->contractId)
            ->get()
            ->groupBy('milestone');

        return view('livewire.contracts.contract-workflow-panel', [
            'steps' => ContractWorkflowStep::STEPS,
            'stepKeys' => ContractWorkflowStep::STEP_KEYS,
            'completedSteps' => $completedSteps,
            'currentStatus' => $currentStatus,
            'filesByStep' => $filesByStep,
            'canEdit' => $this->canEdit(),
        ]);
    }
}
