<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $asset_id
 * @property int|null $assignee_id
 * @property string|null $facility
 * @property string|null $priority
 * @property string|null $description
 * @property string|null $status
 * @property string|null $sgc
 * @property Carbon|null $started_at
 * @property Carbon|null $ended_at
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 * @property-read User|null $requestor
 * @property-read User|null $assignee
 * @property-read Asset|null $asset
 * @property-read Collection<int, MntPItem> $items
 */
final class MntPreventive extends Model
{
    protected $table = 'mnt_preventive';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
            'closed_at'  => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function requestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return HasMany<MntPItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(MntPItem::class, 'mntp_id');
    }
}
