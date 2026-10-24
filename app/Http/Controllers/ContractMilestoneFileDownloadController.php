<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractMilestoneFile;
use App\Services\Contracts\ContractProgressService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ContractMilestoneFileDownloadController extends Controller
{
    public function __invoke(Contract $contract, ContractMilestoneFile $file, ContractProgressService $service): StreamedResponse
    {
        Gate::authorize('view', $contract);
        abort_unless($file->contract_type === Contract::class && (int) $file->contract_id === $contract->id, 404);
        $disk = Storage::disk($service->fileDisk($file));
        abort_unless($disk->exists($file->file_path), 404);

        return $disk->download($file->file_path, basename($file->original_name));
    }
}
