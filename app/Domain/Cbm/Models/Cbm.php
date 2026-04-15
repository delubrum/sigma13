<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Cbm extends Model
{
    protected $table = 'cbm';

    protected $fillable = [
        'project',
        'user_id',
        'total_items',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CbmItem::class, 'cbm_id');
    }
}
