<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'starts_at',
        'ends_at',
        'location',
        'description',
        'duration',
        'fee',
        'instructor',
        'audience',
        'objectives',
        'content_summary',
        'content_detail',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'fee' => 'float',
        ];
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'course_enrollments')
            ->withTimestamps();
    }
}
