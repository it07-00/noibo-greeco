<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Services\Contracts\ContractProgressService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

final class ContractAttachments extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $contractId;

    public array $files = [];

    public function mount(int $contractId): void
    {
        $this->contractId = $contractId;
        Gate::authorize('view', Contract::findOrFail($contractId));
    }

    public function upload(ContractProgressService $service): void
    {
        $service->attach(Contract::findOrFail($this->contractId), $this->files, auth()->user());
        $this->files = [];
        $this->resetValidation();
    }

    public function deleteFile(int $id, ContractProgressService $service): void
    {
        $contract = Contract::findOrFail($this->contractId);
        Gate::authorize('update', $contract);
        $file = $contract->milestoneFiles()->where('milestone', 'contract')->find($id);
        abort_unless($file, 404);
        Storage::disk($service->fileDisk($file))->delete($file->file_path);
        $file->delete();
    }

    public function render(): View
    {
        $contract = Contract::findOrFail($this->contractId);
        Gate::authorize('view', $contract);

        return view('livewire.contracts.contract-attachments', [
            'contract' => $contract,
            'attachments' => $contract->milestoneFiles()->where('milestone', 'contract')->with('uploader')->latest()->get(),
            'canManage' => Gate::allows('update', $contract),
        ]);
    }
}
