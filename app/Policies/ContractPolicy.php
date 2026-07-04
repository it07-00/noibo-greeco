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
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ContractView->value);
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::ContractCreate->value);
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractUpdate->value);
    }

    public function approve(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractApprove->value);
    }

    public function activate(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractActivate->value);
    }

    public function complete(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractComplete->value);
    }

    public function cancel(User $user, Contract $contract): bool
    {
        return $user->can(PermissionEnum::ContractCancel->value);
    }
}
