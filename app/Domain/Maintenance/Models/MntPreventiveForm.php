<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'asset_id',
    'kind',
    'activity',
    'frequency',
    'last_performed_at',
])]
/**
 * @property int $id
 * @property int $asset_id
 * @property string $kind
 * @property string $activity
 * @property string $frequency
 * @property Carbon|null $last_performed_at
 * @property-read Asset $asset
 */
final class MntPreventiveForm extends Model
{
    #[\Override]
    protected $table = 'mnt_preventive_form';

    #[\Override]
    protected function casts(): array
    {
        return [
            'last_performed_at' => 'date',
        ];
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
