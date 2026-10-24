<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\ContractProgressNote;
use App\Models\User;
use App\Notifications\ContractProgressNoted;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class ContractNoteService
{
    public function add(Contract $contract, string $content, User $actor): ContractProgressNote
    {
        Gate::forUser($actor)->authorize('view', $contract);
        $content = trim($content);
        Validator::make(['progressNote' => $content], ['progressNote' => ['required', 'string', 'max:2000']], [
            'progressNote.required' => 'Vui lòng nhập nội dung ghi chú.', 'progressNote.max' => 'Ghi chú không được vượt quá 2.000 ký tự.',
        ])->validate();
        $note = $contract->progressNotes()->create(['created_by' => $actor->id, 'content' => $content, 'reported_at' => today()]);
        $ids = $contract->assignments()->get(['user_id', 'assigned_by'])
            ->flatMap(fn ($assignment) => [$assignment->user_id, $assignment->assigned_by])->filter()->all();
        $ids[] = $contract->owner_id;
        $recipients = User::whereNull('locked_at')->where(fn ($query) => $query->whereIn('id', $ids)
            ->orWhereHas('roles', fn ($query) => $query->where('name', RoleEnum::Director->value)))
            ->whereDoesntHave('roles', fn ($query) => $query->where('name', RoleEnum::IT->value))->get();
        foreach ($recipients as $recipient) {
            if ($recipient->id !== $actor->id && Gate::forUser($recipient)->allows('view', $contract)) {
                $recipient->notify(new ContractProgressNoted($contract->id, $contract->contract_number ?: $contract->title, $actor->name));
            }
        }

        return $note;
    }
}
