<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'ticket_id', 'user_id', 'notes', 'date', 'attends',
])]
/**
 * @property int $id
 * @property int $ticket_id
 * @property int|null $user_id
 * @property string|null $notes
 * @property Carbon|null $date
 * @property string|null $attends
 */
final class TicketItem extends Model
{
    /** @var string */
    #[\Override]
    protected $table = 'ticket_items';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    /** @return BelongsTo<Ticket, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
