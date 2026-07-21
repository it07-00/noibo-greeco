<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MarketingCategory;
use App\Enums\MarketingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class MarketingPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'content',
        'scheduled_at',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'approved_at' => 'datetime',
            'category' => MarketingCategory::class,
            'status' => MarketingPlanStatus::class,
            'created_by' => 'integer',
            'approved_by' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<MarketingPlanImage>
     */
    public function images(): HasMany
    {
        return $this->hasMany(MarketingPlanImage::class)->orderBy('sort_order');
    }
}
