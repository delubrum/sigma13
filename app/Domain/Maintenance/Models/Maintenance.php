<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Assets\Models\Asset;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Maintenance extends Model
{
    protected $table = 'mnt';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'asset_id',
        'facility',
        'priority',
        'description',
        'assignee_id',
        'status',
        'sgc',
        'root_cause',
        'rating',
        'created_at',
        'started_at',
        'ended_at',
        'closed_at',
        'subtype',
        'notes',
        'kind',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'closed_at' => 'datetime',
            'rating' => 'int',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceItem::class, 'mnt_id');
    }
}
