<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractRenewalStatus;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\ContractVoucherStatus;
use App\Enums\PaymentMethod;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'customer_id',
        'handler_id',
        'owner_id',
        'department_id',
        'contract_number',
        'type',
        'status',
        'workflow_status',
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
        'revenue',
        'commission',
        'ncc_payment',
        'ncc_payment_sheet_url',
        'ncc_payment_updated_at',
        'ncc_payment_status',
        'ncc_payment_paid_at',
        'shd_greeco',
        'submitted_at',
        'contract_drive_url',
        'province',
        'info_source',
        'submission_place',
        'report_number',
        'voucher_status',
        'payment_percentage',
        'is_offset',
        'has_room_fund',
        'is_overdue',
        'is_renewal',
        'parent_contract_id',
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
            'revenue' => 'integer',
            'commission' => 'integer',
            'ncc_payment' => 'integer',
            'ncc_payment_updated_at' => 'datetime',
            'ncc_payment_paid_at' => 'date',
            'submitted_at' => 'date',
            'voucher_status' => ContractVoucherStatus::class,
            'payment_percentage' => 'decimal:2',
            'is_offset' => 'boolean',
            'has_room_fund' => 'boolean',
            'is_overdue' => 'boolean',
            'is_renewal' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        self::creating(static function (self $contract): void {
            if (empty($contract->revenue)) {
                $contract->revenue = (int) ($contract->value ?? 0);
            }
            if (empty($contract->commission)) {
                $contract->commission = (int) ($contract->customer_commission ?? 0);
            }
        });
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

    public function handler(): BelongsTo
    {
        return $this->belongsTo(Handler::class)->withTrashed();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole([RoleEnum::SuperAdmin->value, RoleEnum::Director->value, RoleEnum::Accountant->value, RoleEnum::IT->value])
            || $user->can(PermissionEnum::ContractViewAll->value)) {
            return $query;
        }

        return $query->where(fn (Builder $query) => $query->where('owner_id', $user->id)
            ->orWhereHas('assignments', fn (Builder $query) => $query->where('user_id', $user->id)->orWhere('assigned_by', $user->id)));
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

    public function commissionRequests(): HasMany
    {
        return $this->hasMany(CommissionRequest::class)->latest();
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(ContractAssignment::class, 'assignable');
    }

    public function workflowSteps(): MorphMany
    {
        return $this->morphMany(ContractWorkflowStep::class, 'contract');
    }

    public function milestoneFiles(): MorphMany
    {
        return $this->morphMany(ContractMilestoneFile::class, 'contract');
    }
}
