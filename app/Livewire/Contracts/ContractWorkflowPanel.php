<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Models\ContractWorkflowStep;
use App\Services\Contracts\ContractProgressService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

final class ContractWorkflowPanel extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $contractId;

    public array $uploadFiles = [];

    public string $comment = '';

    public ?string $activeStep = null;

    #[Locked]
    public ?string $supplementStep = null;

    public function mount(int $contractId): void
    {
        $this->contractId = $contractId;
        Gate::authorize('view', $this->contract());
    }

    public function canEdit(): bool
    {
        return Gate::allows('updateProgress', $this->contract());
    }

    public function openStep(string $step, ContractProgressService $service): void
    {
        $contract = $this->contract();
        Gate::authorize('updateProgress', $contract);
        abort_unless($service->nextStep($contract) === $step, 422);
        $this->cancelStep();
        $this->activeStep = $step;
    }

    public function openFileSupplement(string $step): void
    {
        $contract = $this->contract();
        Gate::authorize('supplementFiles', $contract);
        abort_unless(in_array($step, ContractWorkflowStep::STEP_KEYS, true)
            && $contract->workflowSteps()->where('action', 'complete')->where('step_name', $step)->exists(), 422);
        $this->cancelStep();
        $this->activeStep = $step;
        $this->supplementStep = $step;
    }

    public function completeStep(ContractProgressService $service): void
    {
        if ($this->supplementStep !== null) {
            $this->saveSupplementFiles($service);

            return;
        }
        $service->complete($this->contract(), $this->activeStep ?? '', $this->comment, $this->uploadFiles, auth()->user());
        $this->cancelStep();
        $this->success('Đã cập nhật tiến độ hợp đồng.');
    }

    public function saveSupplementFiles(ContractProgressService $service): void
    {
        $service->supplement($this->contract(), $this->supplementStep ?? '', $this->uploadFiles, auth()->user());
        $this->cancelStep();
        $this->success('Đã bổ sung file đính kèm.');
    }

    public function deleteFile(int $fileId, ContractProgressService $service): void
    {
        $contract = $this->contract();
        Gate::authorize('updateProgress', $contract);
        $file = $contract->milestoneFiles()->find($fileId);
        abort_unless($file, 404);
        Storage::disk($service->fileDisk($file))->delete($file->file_path);
        $file->delete();
        $this->success('Đã xóa file đính kèm.');
    }

    public function cancelStep(): void
    {
        $this->reset('activeStep', 'supplementStep', 'uploadFiles', 'comment');
        $this->resetValidation();
    }

    public function render(ContractProgressService $service): View
    {
        $contract = $this->contract();
        Gate::authorize('view', $contract);
        $history = $contract->workflowSteps()->where('action', 'complete')->with('user')->orderBy('id')->get();

        return view('livewire.contracts.contract-workflow-panel', [
            'contract' => $contract,
            'steps' => ContractWorkflowStep::STEPS,
            'completedSteps' => $history->pluck('step_name')->unique()->all(),
            'nextStep' => $service->nextStep($contract),
            'savedNotes' => $history,
            'filesByStep' => $contract->milestoneFiles()->whereIn('milestone', ContractWorkflowStep::STEP_KEYS)->with('uploader')->get()->groupBy('milestone'),
            'canEdit' => $this->canEdit(),
            'canSupplement' => Gate::allows('supplementFiles', $contract),
        ]);
    }

    private function contract(): Contract
    {
        return Contract::findOrFail($this->contractId);
    }

    private function success(string $title): void
    {
        $this->dispatch('swal:alert', ['icon' => 'success', 'title' => $title, 'toast' => true, 'position' => 'top-end', 'timer' => 3000]);
        $this->dispatch('contract-workflow:updated');
    }
}
