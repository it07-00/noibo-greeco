<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\ContractPaymentSchedule;
use App\Models\User;

final class ContractPaymentSchedulePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::PaymentScheduleView->value);
    }

    public function view(User $user, ContractPaymentSchedule $schedule): bool
    {
        return $user->can(PermissionEnum::PaymentScheduleView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::PaymentScheduleManage->value);
    }

    public function update(User $user, ContractPaymentSchedule $schedule): bool
    {
        return $user->can(PermissionEnum::PaymentScheduleManage->value);
    }

    public function confirm(User $user, ContractPaymentSchedule $schedule): bool
    {
        return $user->can(PermissionEnum::PaymentScheduleConfirm->value);
    }
}
