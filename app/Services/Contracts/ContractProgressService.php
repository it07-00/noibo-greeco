<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\ContractMilestoneFile;
use App\Models\ContractWorkflowStep;
use App\Models\User;
use App\Notifications\ContractWorkflowUpdated;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ContractProgressService
{
    public const MAX_FILE_KB = 204800;

    public function nextStep(Contract $contract): ?string
    {
        $completed = $contract->workflowSteps()->where('action', 'complete')->pluck('step_name')->all();
        foreach (ContractWorkflowStep::STEP_KEYS as $step) {
            if (! in_array($step, $completed, true)) {
                return $step;
            }
        }

        return null;
    }

    /** @param array<int, UploadedFile> $files */
    public function complete(Contract $contract, string $step, string $comment, array $files, User $actor): void
    {
        Gate::forUser($actor)->authorize('updateProgress', $contract);
        $this->validateFiles($step, $comment, $files, false);
        $stored = [];
        try {
            DB::transaction(function () use ($contract, $step, $comment, $files, $actor, &$stored): void {
                $locked = Contract::whereKey($contract->id)->lockForUpdate()->firstOrFail();
                Gate::forUser($actor)->authorize('updateProgress', $locked);
                if ($this->nextStep($locked) !== $step) {
                    throw ValidationException::withMessages(['activeStep' => 'Vui lòng thực hiện đúng thứ tự; bước này đã hoàn thành hoặc chưa đến lượt.']);
                }
                $this->storeFiles($locked, $step, $files, $actor, $stored);
                $locked->workflowSteps()->create([
                    'user_id' => $actor->id, 'step_name' => $step, 'action' => 'complete', 'comment' => trim($comment) ?: null,
                ]);
                $locked->update(['workflow_status' => $step]);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($stored);
            throw $exception;
        }

        $this->notifyProgress($contract->fresh(), $step, $actor);
    }

    /** @param array<int, UploadedFile> $files */
    public function supplement(Contract $contract, string $step, array $files, User $actor): void
    {
        Gate::forUser($actor)->authorize('supplementFiles', $contract);
        $this->validateFiles($step, '', $files, true);
        $stored = [];
        try {
            DB::transaction(function () use ($contract, $step, $files, $actor, &$stored): void {
                $locked = Contract::whereKey($contract->id)->lockForUpdate()->firstOrFail();
                Gate::forUser($actor)->authorize('supplementFiles', $locked);
                if (! $locked->workflowSteps()->where('action', 'complete')->where('step_name', $step)->exists()) {
                    throw ValidationException::withMessages(['activeStep' => 'Chỉ được bổ sung file cho bước đã hoàn thành.']);
                }
                $this->storeFiles($locked, $step, $files, $actor, $stored);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($stored);
            throw $exception;
        }
    }

    public function fileDisk(ContractMilestoneFile $file): string
    {
        if ($file->storage_disk) {
            return $file->storage_disk;
        }
        $disk = config('filesystems.upload_disk', 'public');

        return Storage::disk($disk)->exists($file->file_path) ? $disk : 'public';
    }

    public function attach(Contract $contract, array $files, User $actor): void
    {
        Gate::forUser($actor)->authorize('update', $contract);
        $this->validateFiles('contract', '', $files, true, true);
        $stored = [];
        try {
            DB::transaction(function () use ($contract, $files, $actor, &$stored): void {
                $locked = Contract::whereKey($contract->id)->lockForUpdate()->firstOrFail();
                Gate::forUser($actor)->authorize('update', $locked);
                $this->storeFiles($locked, 'contract', $files, $actor, $stored);
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($stored);
            throw $exception;
        }
    }

    private function validateFiles(string $step, string $comment, array $files, bool $supplement, bool $attachment = false): void
    {
        Validator::make(['activeStep' => $step, 'comment' => $comment, 'uploadFiles' => $files], [
            'activeStep' => ['required', Rule::in($attachment ? ['contract'] : ContractWorkflowStep::STEP_KEYS)],
            'comment' => ['nullable', 'string', 'max:1000'],
            'uploadFiles' => [$supplement ? 'required' : 'nullable', 'array', 'max:10', $supplement ? 'min:1' : 'min:0'],
            'uploadFiles.*' => ['file', 'max:'.self::MAX_FILE_KB, 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ], [
            'activeStep.in' => 'Bước tiến độ không hợp lệ.',
            'uploadFiles.required' => 'Vui lòng chọn ít nhất một file cần bổ sung.',
            'uploadFiles.min' => 'Vui lòng chọn ít nhất một file cần bổ sung.',
            'uploadFiles.max' => 'Mỗi lần được đính kèm tối đa 10 file.',
            'uploadFiles.*.max' => 'Mỗi file không được vượt quá 200MB.',
            'uploadFiles.*.mimes' => 'Chỉ chấp nhận PDF, Word, Excel, JPG và PNG.',
            'comment.max' => 'Ghi chú không được vượt quá 1.000 ký tự.',
        ])->validate();
    }

    private function storeFiles(Contract $contract, string $step, array $files, User $actor, array &$stored): void
    {
        foreach ($files as $file) {
            $path = $file->store('contract-files/'.$contract->id.'/'.$step, 'local');
            if ($path === false) {
                throw new \RuntimeException('Không thể lưu file đính kèm.');
            }
            $stored[] = $path;
            $contract->milestoneFiles()->create([
                'milestone' => $step, 'storage_disk' => 'local', 'file_path' => $path,
                'original_name' => $file->getClientOriginalName(), 'uploader_id' => $actor->id,
            ]);
        }
    }

    private function notifyProgress(Contract $contract, string $step, User $actor): void
    {
        $roles = [RoleEnum::Director->value];
        if ($step === 'finished') {
            $roles[] = RoleEnum::Accountant->value;
        }
        $assignedIds = $contract->assignments()->get(['user_id', 'assigned_by'])
            ->flatMap(fn ($assignment) => [$assignment->user_id, $assignment->assigned_by])->filter()->all();
        $ids = array_unique([...$assignedIds, $contract->owner_id]);
        $recipients = User::whereNull('locked_at')->where(fn ($query) => $query
            ->whereIn('id', $ids)->orWhereHas('roles', fn ($query) => $query->whereIn('name', $roles)))
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', RoleEnum::IT->value))->get();

        foreach ($recipients as $recipient) {
            if ($recipient->id !== $actor->id && Gate::forUser($recipient)->allows('view', $contract)) {
                $recipient->notify(new ContractWorkflowUpdated($contract->id, $contract->contract_number ?: $contract->title,
                    $step, ContractWorkflowStep::STEPS[$step], $actor->name));
            }
        }
    }
}
