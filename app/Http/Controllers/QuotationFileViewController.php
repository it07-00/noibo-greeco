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
        $fileName = null;

        if (! $filePath || ! Storage::disk('local')->exists($filePath)) {
            $firstFile = $quotation->files()->first();
            if ($firstFile && $firstFile->file_path && Storage::disk('local')->exists($firstFile->file_path)) {
                $filePath = $firstFile->file_path;
                $fileName = $firstFile->file_name;
            }
        }

        abort_unless($filePath && Storage::disk('local')->exists($filePath), 404);

        if (! $fileName) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileName = 'Bao_gia_'.($quotation->quotation_number ?: $quotation->id)
                .($extension !== '' ? '.'.$extension : '');
        }

        return Storage::disk('local')->response(
            $filePath,
            $fileName,
            ['Content-Disposition' => 'inline; filename="'.$fileName.'"'],
        );
    }
}
