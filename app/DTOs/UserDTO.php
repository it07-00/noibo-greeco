<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class UserDTO
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public string $name,
        public string $username,
        public ?string $email = null,
        public ?string $password = null,
        public array $roles = [],
        public ?int $departmentId = null,
        public ?string $dob = null,
        public ?string $address = null,
    ) {}

    /**
     * @param  array{name?: string, username?: string, email?: string|null, password?: string|null, roles?: array<int, string>|null, department_id?: int|string|null, dob?: string|null, address?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) ($data['name'] ?? '')),
            username: strtolower(trim((string) ($data['username'] ?? ''))),
            email: filled($data['email'] ?? null) ? strtolower(trim((string) $data['email'])) : null,
            password: filled($data['password'] ?? null) ? (string) $data['password'] : null,
            roles: array_values(array_filter(array_map(
                static fn (mixed $role): string => trim((string) $role),
                (array) ($data['roles'] ?? []),
            ))),
            departmentId: isset($data['department_id']) && $data['department_id'] !== '' ? (int) $data['department_id'] : null,
            dob: isset($data['dob']) && $data['dob'] !== '' ? (string) $data['dob'] : null,
            address: isset($data['address']) && $data['address'] !== '' ? trim((string) $data['address']) : null,
        );
    }
}
