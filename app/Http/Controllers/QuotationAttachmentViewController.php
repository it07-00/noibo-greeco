<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\QuotationFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class QuotationAttachmentViewController extends Controller
{
    public function __invoke(QuotationFile $quotationFile): StreamedResponse
    {
        $quotation = $quotationFile->quotation;
        Gate::authorize('view', $quotation);

        abort_unless($quotationFile->file_path && Storage::disk('local')->exists($quotationFile->file_path), 404);

        $fileName = $quotationFile->file_name ?: basename($quotationFile->file_path);

        return Storage::disk('local')->response(
            $quotationFile->file_path,
            $fileName,
            ['Content-Disposition' => 'inline; filename="'.$fileName.'"'],
        );
    }
}
