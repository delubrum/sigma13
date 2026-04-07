<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

#[Fillable([
    'user_id',
    'asset_id',
    'facility',
    'subtype',
    'priority',
    'description',
    'status',
    'sgc',
    'root_cause',
    'rating',
    'started_at',
    'closed_at',
])]
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $asset_id
 * @property string|null $facility
 * @property string|null $subtype
 * @property string|null $priority
 * @property string|null $description
 * @property string|null $status
 * @property string|null $sgc
 * @property string|null $root_cause
 * @property string|null $cause
 * @property int|null $rating
 * @property Carbon|null $started_at
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 * @property-read User|null $user
 * @property-read Asset|null $asset
 * @property-read Collection<int, MntItem> $items
 */
final class Mnt extends Model
{
    #[\Override]
    protected $table = 'mnt';

    #[\Override]
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
            'created_at' => 'datetime',
            'rating' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return HasMany<MntItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(MntItem::class, 'mnt_id');
    }
}
