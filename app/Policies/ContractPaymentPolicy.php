<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\ContractPayment;
use App\Models\User;

final class ContractPaymentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::PaymentRecord->value);
    }

    public function update(User $user, ContractPayment $payment): bool
    {
        return $user->can(PermissionEnum::PaymentAdjust->value);
    }

    public function void(User $user, ContractPayment $payment): bool
    {
        return $user->can(PermissionEnum::PaymentAdjust->value);
    }
}
