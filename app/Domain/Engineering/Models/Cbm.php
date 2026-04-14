<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Cbm extends Model
{
    protected $table = 'cbm';

    protected $guarded = [];

    public $timestamps = false;

    public function items(): HasMany
    {
        return $this->hasMany(CbmItem::class);
    }
}
