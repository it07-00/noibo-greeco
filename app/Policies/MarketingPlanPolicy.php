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
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanUpdate->value);
    }

    public function delete(User $user, MarketingPlan $plan): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanDelete->value);
    }

    public function approve(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MarketingPlanApprove->value);
    }
}
