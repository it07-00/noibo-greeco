<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Customer;
use App\Models\User;

final class CustomerPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::CustomerView->value);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can(PermissionEnum::CustomerView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CustomerManage->value);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can(PermissionEnum::CustomerManage->value);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can(PermissionEnum::CustomerManage->value);
    }
}
