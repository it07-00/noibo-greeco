<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class QuotationFileViewController extends Controller
{
    public function __invoke(Quotation $quotation): StreamedResponse
    {
        Gate::authorize('view', $quotation);

        $filePath = $quotation->file_path;
        $fileRecord = $quotation->files()->where('file_path', $filePath)->first() ?? $quotation->files()->first();

        if ($fileRecord && $fileRecord->file_path && Storage::disk('local')->exists($fileRecord->file_path)) {
            $filePath = $fileRecord->file_path;
            $fileName = $fileRecord->file_name;
        } else {
            $fileName = basename((string) $filePath);
        }

        abort_unless($filePath && Storage::disk('local')->exists($filePath), 404);

        return Storage::disk('local')->response(
            $filePath,
            $fileName ?: basename((string) $filePath),
            ['Content-Disposition' => 'inline; filename="'.($fileName ?: basename((string) $filePath)).'"'],
        );
    }
}
