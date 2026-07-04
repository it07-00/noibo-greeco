<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'customer_id',
        'owner_id',
        'department_id',
        'contract_number',
        'type',
        'status',
        'renewal_status',
        'title',
        'value',
        'original_amount',
        'customer_commission',
        'commission_tax',
        'currency',
        'payment_method',
        'signed_at',
        'starts_at',
        'ends_at',
        'completed_at',
        'liquidated_at',
        'suspension_reason',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContractType::class,
            'status' => ContractStatus::class,
            'renewal_status' => ContractRenewalStatus::class,
            'value' => 'integer',
            'original_amount' => 'integer',
            'customer_commission' => 'integer',
            'commission_tax' => 'integer',
            'payment_method' => PaymentMethod::class,
            'signed_at' => 'date',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'completed_at' => 'datetime',
            'liquidated_at' => 'datetime',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ContractService::class)->orderBy('sort_order');
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(ContractProgressNote::class)->latest('reported_at');
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(ContractPaymentSchedule::class)->orderBy('installment_number');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContractPayment::class)->latest('paid_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class)->latest();
    }
}
