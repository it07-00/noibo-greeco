<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Users\EnsureUserCanBeDeleted;
use App\DTOs\UserDTO;
use App\DTOs\UserFilterDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

final class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EnsureUserCanBeDeleted $ensureUserCanBeDeleted,
    ) {}

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(UserFilterDTO $filter): LengthAwarePaginator
    {
        return $this->users->paginate($filter);
    }

    public function find(int $id): User
    {
        return $this->users->find($id);
    }

    public function create(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto): User {
            $user = $this->users->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make((string) $dto->password),
            ]);

            $this->assignRole($user, $dto->roles);

            return $user->refresh()->load('roles:id,name');
        });
    }

    public function update(User $user, UserDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto): User {
            $attributes = [
                'name' => $dto->name,
                'email' => $dto->email,
            ];

            if ($dto->password !== null) {
                $attributes['password'] = Hash::make($dto->password);
            }

            $user = $this->users->update($user, $attributes);
            $this->assignRole($user, $dto->roles);

            return $user->refresh()->load('roles:id,name');
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->ensureUserCanBeDeleted->handle($user);
            $this->users->delete($user);
        });
    }

    /**
     * @param  list<string>  $roles
     */
    public function assignRole(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    /**
     * @return Collection<int, Role>
     */
    public function roleOptions(): Collection
    {
        return Role::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }
}
