<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Models\User;
use App\Notifications\ContractAssigned;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class ContractAssignmentService
{
    public function sync(Contract $contract, array $userIds, string $external, string $deadline, User $actor): void
    {
        Gate::forUser($actor)->authorize('assign', $contract);
        Validator::make(['assignUserIds' => $userIds, 'assignExternal' => $external, 'assignDeadline' => $deadline], [
            'assignUserIds' => ['array', 'max:50'],
            'assignUserIds.*' => ['integer', 'distinct', Rule::exists('users', 'id')->whereNull('deleted_at')->whereNull('locked_at')],
            'assignExternal' => ['nullable', 'string', 'max:191'],
            'assignDeadline' => ['nullable', 'date'],
        ], [], ['assignUserIds' => 'người thực hiện', 'assignUserIds.*' => 'người thực hiện',
            'assignExternal' => 'đơn vị thực hiện bên ngoài', 'assignDeadline' => 'hạn hoàn thành'])->validate();
        $newIds = DB::transaction(function () use ($contract, $userIds, $external, $deadline, $actor): array {
            $locked = Contract::whereKey($contract->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize('assign', $locked);
            $previous = $locked->assignments()->whereNotNull('user_id')->pluck('user_id')->all();
            $locked->assignments()->whereNotNull('user_id')->whereNotIn('user_id', $userIds)->delete();
            foreach ($userIds as $id) {
                $assignment = $locked->assignments()->firstOrNew(['user_id' => $id]);
                if (! $assignment->exists) {
                    $assignment->assigned_by = $actor->id;
                }
                $assignment->deadline = $deadline ?: null;
                $assignment->save();
            }
            if (trim($external) !== '') {
                $assignment = $locked->assignments()->firstOrNew(['user_id' => null]);
                if (! $assignment->exists) {
                    $assignment->assigned_by = $actor->id;
                }
                $assignment->external_assignee = trim($external);
                $assignment->deadline = $deadline ?: null;
                $assignment->save();
            } else {
                $locked->assignments()->whereNull('user_id')->delete();
            }

            return array_values(array_diff($userIds, $previous));
        });
        foreach (User::whereIn('id', $newIds)->whereKeyNot($actor->id)->get() as $user) {
            $user->notify(new ContractAssigned($contract->id, $contract->contract_number ?: $contract->title, $actor->name));
        }
    }
}
