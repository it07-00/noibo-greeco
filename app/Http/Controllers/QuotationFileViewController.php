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
        abort_unless($quotation->file_path && Storage::disk('local')->exists($quotation->file_path), 404);

        $extension = pathinfo($quotation->file_path, PATHINFO_EXTENSION);
        $fileName = 'Bao_gia_'.($quotation->quotation_number ?: $quotation->id)
            .($extension !== '' ? '.'.$extension : '');

        return Storage::disk('local')->response(
            $quotation->file_path,
            $fileName,
            ['Content-Disposition' => 'inline; filename="'.$fileName.'"'],
        );
    }
}
