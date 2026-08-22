<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'owner_id',
        'quotation_number',
        'contract_type',
        'status',
        'issued_at',
        'valid_until',
        'total_amount',
        'original_amount',
        'customer_commission',
        'commission_tax',
        'contract_value',
        'currency',
        'working_situation',
        'notes',
        'file_path',
        'lost_reason',
        'sent_at',
        'won_at',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'contract_type' => ContractType::class,
            'status' => QuotationStatus::class,
            'issued_at' => 'date',
            'valid_until' => 'date',
            'total_amount' => 'integer',
            'original_amount' => 'integer',
            'customer_commission' => 'integer',
            'commission_tax' => 'integer',
            'contract_value' => 'integer',
            'sent_at' => 'datetime',
            'won_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(QuotationService::class)->orderBy('sort_order');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(QuotationFollowUp::class)->latest('contacted_at');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(QuotationVersion::class)->orderByDesc('version');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(QuotationFile::class)->orderBy('sort_order')->orderBy('id');
    }
}
