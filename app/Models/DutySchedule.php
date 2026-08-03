<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

final class DutySchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_at',
        'end_at',
        'check_in_at',
        'check_out_at',
        'late_minutes',
        'early_minutes',
        'label_color',
        'is_private',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'late_minutes' => 'integer',
            'early_minutes' => 'integer',
            'is_private' => 'boolean',
            'created_by' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'duty_schedule_user');
    }

    public function getCombinedParticipantsAttribute()
    {
        $local = $this->users->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'system' => 'greeco',
        ]);

        $baochau = DB::table('duty_schedule_user')
            ->where('duty_schedule_id', $this->id)
            ->whereNotNull('baochau_user_id')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->baochau_user_id,
                'name' => 'Bảo Châu: '.$row->baochau_user_name,
                'system' => 'baochau',
            ]);

        return $local->concat($baochau);
    }
}
