<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CbmItem extends Model
{
    protected $table = 'cbm_items';
    public $timestamps = false; // Legacy table might not have them

    protected $fillable = [
        'cbm_id',
        'width',
        'height',
        'item_length',
        'weight',
        'qty',
    ];

    public function cbm(): BelongsTo
    {
        return $this->belongsTo(Cbm::class, 'cbm_id');
    }
}
