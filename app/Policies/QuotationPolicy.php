<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Quotation;
use App\Models\User;

final class QuotationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['update', 'delete'], true)) {
            return null;
        }

        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::QuotationView->value);
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return $user->can(PermissionEnum::QuotationView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::QuotationCreate->value);
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if ($quotation->contract()->exists()) {
            return false;
        }

        return $user->can(PermissionEnum::QuotationUpdate->value);
    }

    public function send(User $user, Quotation $quotation): bool
    {
        return $user->can(PermissionEnum::QuotationSend->value);
    }

    public function convert(User $user, Quotation $quotation): bool
    {
        return $user->can(PermissionEnum::QuotationConvert->value);
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        if ($quotation->status === \App\Enums\QuotationStatus::Won) {
            return false;
        }

        return $user->can(PermissionEnum::QuotationUpdate->value);
    }
}
