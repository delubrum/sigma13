<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'expiry',
    'code',
    'url',
    'asset_id',
    'user_id',
])]
class AssetDocument extends Model
{
    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'expiry' => 'date',
            'asset_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
