<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Models;

use App\Domain\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'preventive_id', 'scheduled_start', 'scheduled_end', 'started', 'attended', 
    'closed_at', 'status', 'kind', 'asset_id', 'activity',
])]
final class MntPreventive extends Model
{
    /** @var string */
    protected $table = 'mnt_preventive';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'scheduled_start' => 'datetime',
            'scheduled_end'   => 'datetime',
            'started'         => 'datetime',
            'attended'        => 'datetime',
            'closed_at'       => 'datetime',
        ];
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return BelongsTo<MntPreventiveForm, $this> */
    public function form(): BelongsTo
    {
        return $this->belongsTo(MntPreventiveForm::class, 'preventive_id');
    }

    /** @return HasMany<MntpItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(MntpItem::class, 'mntp_id');
    }
}
