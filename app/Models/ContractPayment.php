<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ContractPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'paid_at',
        'amount',
        'payment_method',
        'reference_number',
        'proof_file_path',
        'recorded_by',
        'notes',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'date',
            'amount' => 'integer',
            'payment_method' => PaymentMethod::class,
            'voided_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ContractPaymentAllocation::class, 'payment_id');
    }
}
