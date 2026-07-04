<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ContractDocumentDownloadController extends Controller
{
    public function __invoke(Contract $contract, ContractDocument $document): StreamedResponse
    {
        abort_unless($document->contract_id === $contract->id, 404);
        Gate::authorize('view', $document);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $fileName = $document->title.($extension !== '' ? '.'.$extension : '');

        return Storage::disk('local')->download($document->file_path, $fileName);
    }
}
