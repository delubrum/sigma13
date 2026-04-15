<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MaintenanceItem extends Model
{
    protected $table = 'mnt_items';
    public $timestamps = false;

    protected $fillable = [
        'mnt_id',
        'user_id',
        'created_at',
        'duration',
        'complexity',
        'attends',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'duration' => 'float',
        ];
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'mnt_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
