<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketItem;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateTicketStatusAction
{
    use AsAction;

    public function handle(int $id, string $status, ?string $reason = null, ?int $userId = null): Ticket
    {
        return DB::transaction(function () use ($id, $status, $reason, $userId) {
            $ticket = Ticket::query()->lockForUpdate()->findOrFail($id);
            
            $updateData = ['status' => $status];
            
            if ($status === 'Closed') {
                $updateData['closed_at'] = now();
            }

            $ticket->update($updateData);

            if ($reason || $status === 'Closed' || $status === 'Rejected') {
                TicketItem::create([
                    'ticket_id' => $id,
                    'user_id'   => $userId,
                    'notes'     => match($status) {
                        'Closed'   => 'TICKET CERRADO.',
                        'Rejected' => 'RECHAZADO: ' . ($reason ?? 'Sin motivo especificado.'),
                        default    => $reason ?? "Estado actualizado a {$status}",
                    },
                    'date'      => now(),
                ]);
            }

            return $ticket;
        });
    }
}
