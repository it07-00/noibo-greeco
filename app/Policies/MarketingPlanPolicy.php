<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\MarketingPlan;
use App\Models\User;

final class MarketingPlanPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanView->value);
    }

    public function view(User $user, MarketingPlan $plan): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanView->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanCreate->value);
    }

    public function update(User $user, MarketingPlan $plan): bool
    {
        if (! $user->hasPermissionTo(PermissionEnum::MarketingPlanUpdate->value)) {
            return false;
        }

        // If user has approve permission, they can edit anytime
        if ($user->hasPermissionTo(PermissionEnum::MarketingPlanApprove->value)) {
            return true;
        }

        // Creators can edit if draft or rejected or pending
        return (int) $plan->created_by === (int) $user->id;
    }

    public function delete(User $user, MarketingPlan $plan): bool
    {
        if (! $user->hasPermissionTo(PermissionEnum::MarketingPlanDelete->value)) {
            return false;
        }

        if ($user->hasPermissionTo(PermissionEnum::MarketingPlanApprove->value)) {
            return true;
        }

        return (int) $plan->created_by === (int) $user->id;
    }

    public function approve(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanApprove->value);
    }
}
