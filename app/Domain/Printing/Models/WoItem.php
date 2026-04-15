<?php

declare(strict_types=1);

namespace App\Domain\Printing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WoItem extends Model
{
    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected $table = 'wo_items';

    #[\Override]
    protected $fillable = ['wo_code', 'code', 'description', 'fuc', 'qty'];

    /** @return BelongsTo<Wo, $this> */
    public function wo(): BelongsTo
    {
        return $this->belongsTo(Wo::class, 'wo_code', 'code');
    }
}
