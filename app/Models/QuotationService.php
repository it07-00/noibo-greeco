<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class QuotationService extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'service_type',
        'description',
        'quantity',
        'unit_price',
        'total_amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'service_type' => ServiceType::class,
            'quantity' => 'decimal:2',
            'unit_price' => 'integer',
            'total_amount' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
