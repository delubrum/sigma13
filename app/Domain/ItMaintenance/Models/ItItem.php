<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'it_id', 'user_id', 'complexity', 'attends', 'duration', 'notes',
])]
final class ItItem extends Model
{
    /** @var string */
    protected $table = 'it_items';

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

    /** @return BelongsTo<It, $this> */
    public function it(): BelongsTo
    {
        return $this->belongsTo(It::class, 'it_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
