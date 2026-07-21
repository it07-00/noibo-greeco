<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\MarketingPlanDTO;
use App\Enums\MarketingPlanStatus;
use App\Models\MarketingPlan;
use App\Models\MarketingPlanImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class MarketingPlanService
{
    /**
     * @return Collection<int, MarketingPlan>
     */
    public function getEventsInRange(string $start, string $end, ?string $category = null, ?string $status = null, int|string|null $createdBy = null): Collection
    {
        $query = MarketingPlan::query()
            ->with(['creator', 'approver', 'images'])
            ->whereBetween('scheduled_at', [$start, $end]);

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($createdBy && (int) $createdBy > 0) {
            $query->where('created_by', (int) $createdBy);
        }

        return $query->orderBy('scheduled_at', 'asc')->get();
    }

    /**
     * @param array<int, UploadedFile> $newImages
     */
    public function create(MarketingPlanDTO $dto, array $newImages = []): MarketingPlan
    {
        return DB::transaction(function () use ($dto, $newImages): MarketingPlan {
            $plan = MarketingPlan::create([
                'title' => $dto->title,
                'category' => $dto->category,
                'content' => $dto->content,
                'scheduled_at' => $dto->scheduled_at,
                'status' => $dto->status,
                'notes' => $dto->notes,
                'created_by' => $dto->created_by ?? auth()->id(),
            ]);

            $this->storeImages($plan, $newImages);

            return $plan->fresh(['creator', 'images']);
        });
    }

    /**
     * @param array<int, UploadedFile> $newImages
     * @param array<int, int> $deleteImageIds
     */
    public function update(MarketingPlan $plan, MarketingPlanDTO $dto, array $newImages = [], array $deleteImageIds = []): MarketingPlan
    {
        return DB::transaction(function () use ($plan, $dto, $newImages, $deleteImageIds): MarketingPlan {
            $plan->update([
                'title' => $dto->title,
                'category' => $dto->category,
                'content' => $dto->content,
                'scheduled_at' => $dto->scheduled_at,
                'status' => $dto->status,
                'notes' => $dto->notes,
            ]);

            if (! empty($deleteImageIds)) {
                $imagesToDelete = MarketingPlanImage::whereIn('id', $deleteImageIds)
                    ->where('marketing_plan_id', $plan->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->file_path)) {
                        Storage::disk('public')->delete($image->file_path);
                    }
                    $image->delete();
                }
            }

            $this->storeImages($plan, $newImages);

            return $plan->fresh(['creator', 'approver', 'images']);
        });
    }

    public function approve(MarketingPlan $plan): MarketingPlan
    {
        $plan->update([
            'status' => MarketingPlanStatus::Approved->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return $plan->fresh(['creator', 'approver', 'images']);
    }

    public function reject(MarketingPlan $plan, string $reason): MarketingPlan
    {
        $plan->update([
            'status' => MarketingPlanStatus::Rejected->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $plan->fresh(['creator', 'approver', 'images']);
    }

    public function submitForApproval(MarketingPlan $plan): MarketingPlan
    {
        $plan->update([
            'status' => MarketingPlanStatus::Pending->value,
        ]);

        return $plan->fresh();
    }

    public function delete(MarketingPlan $plan): void
    {
        DB::transaction(function () use ($plan): void {
            foreach ($plan->images as $image) {
                if (Storage::disk('public')->exists($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }
                $image->delete();
            }
            $plan->delete();
        });
    }

    /**
     * @param array<int, UploadedFile> $files
     */
    private function storeImages(MarketingPlan $plan, array $files): void
    {
        $maxOrder = (int) $plan->images()->max('sort_order') ?: 0;

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('marketing-plans/'.$plan->id, 'public');

            MarketingPlanImage::create([
                'marketing_plan_id' => $plan->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'sort_order' => $maxOrder + $index + 1,
            ]);
        }
    }
}
