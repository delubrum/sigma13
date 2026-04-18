<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $kind
 * @property string|null $category
 * @property string $priority
 * @property string|null $urgency
 * @property string $facility
 * @property int $reporter_id
 * @property int|null $assignee_id
 * @property int|null $asset_id
 * @property string $description
 * @property string|null $reason
 * @property string|null $root_cause
 * @property string|null $sgc_code
 * @property string|null $reference_url
 * @property string|null $status
 * @property string|null $complexity
 * @property int|null $rating
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 */
#[Fillable([
    'kind', 'category', 'priority', 'urgency', 'facility',
    'reporter_id', 'assignee_id', 'asset_id',
    'description', 'reason', 'root_cause', 'sgc_code',
    'reference_url', 'status', 'complexity', 'rating',
    'assigned_at', 'started_at', 'verified_at', 'ended_at', 'closed_at',
])]
class Issue extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const UPDATED_AT = null;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at'  => 'datetime',
            'assigned_at' => 'datetime',
            'started_at'  => 'datetime',
            'verified_at' => 'datetime',
            'ended_at'    => 'datetime',
            'closed_at'   => 'datetime',
            'rating'      => 'integer',
            'asset_id'    => 'integer',
            'reporter_id' => 'integer',
            'assignee_id' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
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

    /** @return HasMany<IssueItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(IssueItem::class, 'issue_id')->orderByDesc('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->useDisk('r2')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/heic']);
    }


}
