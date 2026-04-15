<?php

declare(strict_types=1);

namespace App\Domain\Printing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Wo extends Model
{
    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected $table = 'wo';

    #[\Override]
    protected $fillable = ['code', 'project', 'user_id', 'es_id', 'created_at'];

    #[\Override]
    protected $casts = [
        'created_at' => 'datetime',
    ];

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** @return HasMany<WoItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(WoItem::class, 'wo_code', 'code');
    }
}
