<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Contract;
use App\Models\User;

final class ContractPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($ability === 'supplementFiles') {
            return null;
        }

        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ContractView->value);
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractView->value)
            && Contract::query()->visibleTo($user)->whereKey($contract->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::ContractCreate->value);
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractUpdate->value) && $this->view($user, $contract);
    }

    public function assign(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractAssign->value) && $this->view($user, $contract);
    }

    public function updateProgress(User $user, Contract $contract): bool
    {
        if (! $this->view($user, $contract)) {
            return false;
        }

        if ($user->hasAnyRole([RoleEnum::Director->value, RoleEnum::IT->value])) {
            return true;
        }

        return $user->can(PermissionEnum::ContractUpdate->value)
            && $contract->assignments()->where('user_id', $user->id)->exists();
    }

    public function supplementFiles(User $user, Contract $contract): bool
    {
        return $this->updateProgress($user, $contract)
            && $contract->assignments()->where('user_id', $user->id)->exists();
    }

    public function approve(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractApprove->value) && $this->view($user, $contract);
    }

    public function activate(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractActivate->value) && $this->view($user, $contract);
    }

    public function complete(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractComplete->value) && $this->view($user, $contract);
    }

    public function cancel(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractCancel->value) && $this->view($user, $contract);
    }
}
