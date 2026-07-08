<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommissionRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class CommissionRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'user_id',
        'receiver_name',
        'receiver_phone',
        'bank_account',
        'bank_code',
        'bank_number',
        'amount',
        'referrer_info',
        'notes',
        'status',
        'processed_at',
        'processed_by',
        'payment_bill_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => CommissionRequestStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getQrUrlAttribute(): string
    {
        if (! $this->bank_code || ! $this->bank_number) {
            return '';
        }

        $contractNumber = $this->contract?->contract_number ?: ('#'.$this->contract_id);
        $receiverName = strtoupper(Str::ascii($this->receiver_name ?: ''));
        $description = "Chi hoa hong HD {$contractNumber}";

        $query = [
            'addInfo' => $description,
            'accountName' => $receiverName,
        ];

        if ($this->amount > 0) {
            $query['amount'] = $this->amount;
        }

        return "https://img.vietqr.io/image/{$this->bank_code}-{$this->bank_number}-compact2.png?" . http_build_query($query);
    }
}
