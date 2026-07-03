<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\DocumentRegulation;
use App\Models\User;

final class DocumentRegulationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DocumentView->value);
    }

    public function view(User $user, DocumentRegulation $regulation): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DocumentView->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DocumentManage->value);
    }

    public function update(User $user, DocumentRegulation $regulation): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DocumentManage->value);
    }

    public function delete(User $user, DocumentRegulation $regulation): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DocumentManage->value);
    }
}
