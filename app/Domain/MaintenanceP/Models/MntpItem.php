<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'mntp_id', 'user_id', 'complexity', 'attends', 'duration', 'notes',
])]
final class MntpItem extends Model
{
    /** @var string */
    protected $table = 'mntp_items';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'duration'   => 'float',
        ];
    }

    /** @return BelongsTo<MntPreventive, $this> */
    public function preventive(): BelongsTo
    {
        return $this->belongsTo(MntPreventive::class, 'mntp_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
