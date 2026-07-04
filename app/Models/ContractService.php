<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContractService extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'service_type',
        'description',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'service_type' => ServiceType::class,
            'amount' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
