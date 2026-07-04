<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentConditionType;
use App\Enums\PaymentHandoverStatus;
use App\Enums\PaymentScheduleStatus;
use App\Enums\PaymentTermUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ContractPaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'installment_number',
        'name',
        'percentage',
        'amount',
        'condition_type',
        'custom_condition',
        'expected_trigger_date',
        'triggered_at',
        'payment_term_days',
        'payment_term_unit',
        'due_date',
        'status',
        'handover_status',
        'responsible_department_id',
        'responsible_user_id',
        'next_action',
        'next_action_due_at',
        'confirmed_at',
        'confirmed_by',
        'notes',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'installment_number' => 'integer',
            'percentage' => 'decimal:2',
            'amount' => 'integer',
            'condition_type' => PaymentConditionType::class,
            'expected_trigger_date' => 'date',
            'triggered_at' => 'date',
            'payment_term_days' => 'integer',
            'payment_term_unit' => PaymentTermUnit::class,
            'due_date' => 'date',
            'status' => PaymentScheduleStatus::class,
            'handover_status' => PaymentHandoverStatus::class,
            'next_action_due_at' => 'date',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function responsibleDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'responsible_department_id');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ContractPaymentAllocation::class, 'payment_schedule_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class, 'payment_schedule_id');
    }
}
