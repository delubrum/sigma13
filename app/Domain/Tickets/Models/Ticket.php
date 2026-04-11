<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'facility', 'priority', 'status', 'description', 'kind', 
    'started_at', 'closed_at', 'assignee_id'
])]
final class Ticket extends Model
{
    /** @var string */
    protected $table = 'tickets';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'started_at' => 'datetime',
            'closed_at'  => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** @return HasMany<TicketItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(TicketItem::class, 'ticket_id');
    }
}
