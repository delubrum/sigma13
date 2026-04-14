<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $facility
 * @property string $priority
 * @property string $status
 * @property string|null $description
 * @property string $kind
 * @property Carbon|null $created_at
 * @property Carbon|null $started_at
 * @property Carbon|null $closed_at
 * @property int|null $assignee_id
 * @property int|null $asset_id
 * @property string|null $sgc
 * @property string|null $root_cause
 */
#[Fillable([
    'user_id', 'facility', 'priority', 'status', 'description', 'kind',
    'started_at', 'closed_at', 'assignee_id', 'asset_id', 'sgc', 'root_cause',
])]
final class Ticket extends Model
{
    #[\Override]
    protected $table = 'tickets';

    #[\Override]
    public $timestamps = false;

    /**
     * Lógica de Dominio para la creación de Tickets.
     * Esto evita errores de "Not null violation" en el Action Shared.
     */
    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (self $ticket): void {
            // Asigna el usuario autenticado automáticamente
            if (auth()->check()) {
                $ticket->user_id ??= (int) auth()->id();
            }

            // Define un estado inicial por defecto
            $ticket->status ??= 'open';

            // Si no usas timestamps automáticos pero quieres la fecha de creación
            $ticket->created_at ??= now();
        });
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
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
