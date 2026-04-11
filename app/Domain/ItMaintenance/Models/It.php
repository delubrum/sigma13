<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'facility', 'asset_id', 'priority', 'status', 'description', 
    'assignee_id', 'started_at', 'ended_at', 'closed_at', 'sgc', 'rating',
])]
final class It extends Model
{
    /** @var string */
    protected $table = 'it';

    #[\Override]
    public $timestamps = false; // Legacy table doesn't have standard timestamps or uses created_at only

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
            'closed_at'  => 'datetime',
            'rating'     => 'integer',
        ];
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** @return HasMany<ItItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ItItem::class, 'it_id');
    }
}
