<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Actions;

use App\Domain\Maintenance\Models\MntPreventive;
use App\Domain\MaintenanceP\Models\MntPItem;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Update
{
    use AsAction;
    use HtmxOrchestrator;

    public function asController(Request $request, int $id): JsonResponse
    {
        $field = $request->string('field')->toString();
        $ticket = MntPreventive::findOrFail($id);

        match ($field) {
            'scheduled_start' => $ticket->update(['scheduled_start' => $request->string('value')->toString()]),
            'scheduled_end' => $ticket->update(['scheduled_end' => $request->string('value')->toString()]),

            // Attend
            'attended' => (function () use ($ticket): void {
                if (! MntPItem::where('mntp_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Agrega al menos una tarea antes de atender.');
                }
                $ticket->update(['attended' => now()->format('Y-m-d H:i'), 'status' => 'Attended']);
            })(),

            // Close
            'closed_at' => (function () use ($ticket): void {
                if (! MntPItem::where('mntp_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Agrega al menos una tarea antes de cerrar.');
                }
                $ticket->update(['closed_at' => now(), 'status' => 'Closed']);
            })(),

            // Reject
            'reason' => $ticket->update([
                'status' => 'Rejected',
                'activity' => ($ticket->activity ?? '').'

[Rejected] '.$request->string('reason')->toString(),
            ]),

            default => abort(422, "Campo no permitido: {$field}"),
        };

        $this->hxNotify('Guardado');

        return $this->hxResponse();
    }
}
