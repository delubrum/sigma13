<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable([
    'name',
    'profile',
    'email',
    'phone',
    'department',
    'position',
    'status',
    'hire_date',
    'salary',
    'user_id',
])]
#[Hidden(['user_id'])]
class Employee extends Authenticatable
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<AssetEvent, $this> */
    public function assetEvents(): HasMany
    {
        return $this->hasMany(AssetEvent::class, 'employee_id');
    }
}
