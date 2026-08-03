<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class DutyScheduleDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $location,
        public string $start_at,
        public ?string $end_at,
        public ?string $check_in_at = null,
        public ?string $check_out_at = null,
        public ?int $late_minutes = 0,
        public ?int $early_minutes = 0,
        public string $label_color = 'primary',
        public bool $is_private = false,
        public ?int $created_by = null,
        public array $userIds = [],
    ) {}

    /**
     * @param array{
     *     title: string,
     *     description?: string|null,
     *     location?: string|null,
     *     start_at: string,
     *     end_at?: string|null,
     *     check_in_at?: string|null,
     *     check_out_at?: string|null,
     *     late_minutes?: int|null,
     *     early_minutes?: int|null,
     *     label_color?: string|null,
     *     is_private?: bool|null,
     *     created_by?: int|null,
     *     user_ids?: array|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: trim($data['title']),
            description: isset($data['description']) && filled($data['description']) ? trim($data['description']) : null,
            location: isset($data['location']) && filled($data['location']) ? trim($data['location']) : null,
            start_at: $data['start_at'],
            end_at: isset($data['end_at']) && filled($data['end_at']) ? $data['end_at'] : null,
            check_in_at: isset($data['check_in_at']) && filled($data['check_in_at']) ? $data['check_in_at'] : null,
            check_out_at: isset($data['check_out_at']) && filled($data['check_out_at']) ? $data['check_out_at'] : null,
            late_minutes: isset($data['late_minutes']) ? (int) $data['late_minutes'] : 0,
            early_minutes: isset($data['early_minutes']) ? (int) $data['early_minutes'] : 0,
            label_color: $data['label_color'] ?? 'primary',
            is_private: (bool) ($data['is_private'] ?? false),
            created_by: $data['created_by'] ?? null,
            userIds: $data['user_ids'] ?? [],
        );
    }
}
