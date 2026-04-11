<?php

declare(strict_types=1);

namespace App\Domain\Assets\Models;

use App\Domain\Users\Models\Employee;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'kind',
    'asset_id',
    'employee_id',
    'software',
    'hardware',
    'notes',
    'created_at',
    'user_id',
    'wipe',
    'expiry',
])]
#[Hidden(['user_id'])]
class AssetEvent extends Model implements HasMedia
{
    use InteractsWithMedia;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'expiry' => 'date',
            'asset_id' => 'integer',
            'employee_id' => 'integer',
            'user_id' => 'integer',
            'hardware' => 'array',
            'software' => 'array',
            'wipe' => 'boolean',
        ];
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
