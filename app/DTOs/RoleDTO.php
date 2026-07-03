<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class RoleDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $departmentId = null,
    ) {}

    /**
     * @param array{name?: string, description?: string|null, department_id?: int|string|null} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) ($data['name'] ?? '')),
            description: filled($data['description'] ?? null) ? trim((string) $data['description']) : null,
            departmentId: isset($data['department_id']) && $data['department_id'] !== '' ? (int) $data['department_id'] : null,
        );
    }
}
