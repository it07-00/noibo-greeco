<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

final class EnsureUserCanBeDeleted
{
    /**
     * @throws ValidationException
     */
    public function handle(User $user): void
    {
        if (! $user->hasRole(RoleEnum::SuperAdmin->value)) {
            return;
        }

        $superAdminRole = Role::query()
            ->where('name', RoleEnum::SuperAdmin->value)
            ->first();

        if ($superAdminRole === null || $superAdminRole->users()->count() > 1) {
            return;
        }

        throw ValidationException::withMessages([
            'user' => 'Cannot delete the last Super Admin account.',
        ]);
    }
}
