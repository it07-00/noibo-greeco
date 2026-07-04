<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\ContractDocument;
use App\Models\User;

final class ContractDocumentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ContractDocumentView->value);
    }

    public function view(User $user, ContractDocument $document): bool
    {
        return $user->can(PermissionEnum::ContractDocumentView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::ContractDocumentSubmit->value);
    }

    public function review(User $user, ContractDocument $document): bool
    {
        return $user->can(PermissionEnum::ContractDocumentReview->value);
    }
}
