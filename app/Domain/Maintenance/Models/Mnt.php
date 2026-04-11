<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Mnt extends Model
{
    /** @var string */
    protected $table = 'mnt'; // Tu tabla de correctivos

    #[\Override]
    public $timestamps = false;

    /** @var array<string, string> */
    protected $casts = [
        'created_at' => 'datetime',
        'ended_at'   => 'datetime',
        'rating'     => 'integer',
    ];

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
}