<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Data\ItemUpsertData;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketItem;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateActivityAction
{
    use AsAction;

    public function handle(int $ticketId, ItemUpsertData $data, int $currentUserId): TicketItem
    {
        return DB::transaction(function () use ($ticketId, $data, $currentUserId) {
            $ticket = Ticket::query()->lockForUpdate()->findOrFail($ticketId);

            // Lógica legacy: Si es el primer avance, marcar como Started y asignar el ticket
            if ($ticket->status === 'Open') {
                $ticket->update([
                    'status' => 'Started',
                    'started_at' => now(),
                    'assignee_id' => $currentUserId,
                ]);
            }

            return TicketItem::create([
                'ticket_id' => $ticketId,
                'user_id'   => $currentUserId,
                'attends'   => $data->attends,
                'notes'     => $data->notes,
                'date'      => now(),
            ]);
        });
    }
}
