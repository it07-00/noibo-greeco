<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class MarketingPlanDTO
{
    public function __construct(
        public string $title,
        public string $category,
        public ?string $content,
        public string $scheduled_at,
        public string $status = 'draft',
        public ?string $notes = null,
        public ?int $created_by = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: trim((string) $data['title']),
            category: (string) ($data['category'] ?? 'website'),
            content: isset($data['content']) && filled($data['content']) ? trim((string) $data['content']) : null,
            scheduled_at: (string) $data['scheduled_at'],
            status: (string) ($data['status'] ?? 'draft'),
            notes: isset($data['notes']) && filled($data['notes']) ? trim((string) $data['notes']) : null,
            created_by: isset($data['created_by']) ? (int) $data['created_by'] : null,
        );
    }
}
