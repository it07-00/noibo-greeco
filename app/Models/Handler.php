<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Handler extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'address'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
