<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContractProgressNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'created_by',
        'content',
        'progress_percentage',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'reported_at' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
