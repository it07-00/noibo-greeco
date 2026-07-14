<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\User;

final class CoursePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::CourseView->value);
    }

    public function view(User $user, Course $course): bool
    {
        return $user->can(PermissionEnum::CourseView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CourseManage->value);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->can(PermissionEnum::CourseManage->value);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->can(PermissionEnum::CourseManage->value);
    }
}
