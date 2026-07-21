<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class MarketingPlanImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketing_plan_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'sort_order',
    ];

    /**
     * @return BelongsTo<MarketingPlan, $this>
     */
    public function marketingPlan(): BelongsTo
    {
        return $this->belongsTo(MarketingPlan::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
