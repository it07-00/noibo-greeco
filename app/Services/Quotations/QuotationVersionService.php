<?php

declare(strict_types=1);

namespace App\Services\Quotations;

use App\Models\Quotation;
use App\Models\QuotationVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class QuotationVersionService
{
    public function capture(
        Quotation $quotation,
        ?User $actor = null,
        ?string $changeNote = null,
    ): QuotationVersion {
        return DB::transaction(function () use ($quotation, $actor, $changeNote): QuotationVersion {
            $lockedQuotation = Quotation::query()
                ->with(['customer', 'services'])
                ->lockForUpdate()
                ->findOrFail($quotation->id);

            $nextVersion = ((int) $lockedQuotation->versions()->max('version')) + 1;

            return $lockedQuotation->versions()->create([
                'version' => $nextVersion,
                'snapshot' => [
                    'quotation' => $lockedQuotation->withoutRelations()->toArray(),
                    'customer' => $lockedQuotation->customer?->toArray(),
                    'services' => $lockedQuotation->services->toArray(),
                ],
                'created_by' => $actor?->id,
                'change_note' => $changeNote,
            ]);
        });
    }
}
