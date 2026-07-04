<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContractPaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'payment_schedule_id',
        'allocated_amount',
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'integer',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(ContractPayment::class, 'payment_id');
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(ContractPaymentSchedule::class, 'payment_schedule_id');
    }
}
