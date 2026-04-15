<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Preoperational extends Model
{
    #[\Override]
    protected $table = 'preoperational';

    #[\Override]
    protected $guarded = [];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Asset, $this> */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'vehicle_id');
    }

    /** @return HasMany<PreoperationalItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PreoperationalItem::class, 'preop_id');
    }
}
