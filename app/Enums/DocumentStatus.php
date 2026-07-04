<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum DocumentStatus: string
{
    use HasLocalizedOptions;

    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case RevisionRequired = 'revision_required';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Archived = 'archived';

    public static function translationKey(): string
    {
        return 'document_status';
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary-subtle text-body',
            self::Submitted => 'bg-info-subtle text-info-emphasis',
            self::UnderReview => 'bg-warning-subtle text-warning-emphasis',
            self::RevisionRequired => 'bg-warning-subtle text-warning-emphasis',
            self::Approved => 'bg-success-subtle text-success-emphasis',
            self::Rejected => 'bg-danger-subtle text-danger-emphasis',
            self::Archived => 'bg-secondary-subtle text-body',
        };
    }
}
