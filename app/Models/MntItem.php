<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'mnt_id',
    'description',
    'duration',
    'status',
])]
/**
 * @property int $id
 * @property int $mnt_id
 * @property string|null $description
 * @property float|null $duration
 * @property string|null $status
 * @property-read Mnt $mnt
 */
final class MntItem extends Model
{
    #[\Override]
    protected $table = 'mnt_items';

    #[\Override]
    protected function casts(): array
    {
        return [
            'duration' => 'float',
        ];
    }

    /** @return BelongsTo<Mnt, $this> */
    public function mnt(): BelongsTo
    {
        return $this->belongsTo(Mnt::class, 'mnt_id');
    }
}
