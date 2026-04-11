<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Assets\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MntPreventiveForm extends Model
{
    protected $table = 'mnt_preventive_form';

    protected $fillable = [
        'asset_id',
        'kind',
        'activity',
        'frequency',
        'last_performed_at',
        'status',
    ];

    protected $casts = [
        'last_performed_at' => 'date',
        'status' => 'boolean',
    ];

    public $timestamps = false; // La tabla tiene created_at pero no updated_at

    /**
     * @return BelongsTo<Asset, self>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
}
