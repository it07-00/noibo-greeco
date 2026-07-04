<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ContractDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'payment_schedule_id',
        'supersedes_id',
        'type',
        'status',
        'title',
        'file_path',
        'version',
        'submitted_by',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'review_feedback',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'status' => DocumentStatus::class,
            'version' => 'integer',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'expires_at' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(ContractPaymentSchedule::class, 'payment_schedule_id');
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(self::class, 'supersedes_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
