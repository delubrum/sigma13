<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Models\Mnt;
use App\Domain\Maintenance\Models\MntItem;
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
        $ticket = Mnt::findOrFail($id);

        match ($field) {
            'asset_id' => $ticket->update(['asset_id' => $request->input('value')]),

            'priority' => $ticket->update(['priority' => $request->string('value')->toString()]),

            'sgc' => $ticket->update(['sgc' => $request->string('value')->toString()]),

            'root_cause' => $ticket->update(['root_cause' => $request->string('value')->toString()]),

            'assignee_id' => $ticket->update(['assignee_id' => $request->input('value')]),

            // Attend
            'ended_at' => (function () use ($ticket): void {
                if (! MntItem::where('mnt_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Agrega al menos una tarea antes de atender.');
                }
                $ticket->update(['ended_at' => now(), 'status' => 'Attended']);
            })(),

            // Close
            'closed_at' => (function () use ($ticket): void {
                if (! MntItem::where('mnt_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Agrega al menos una tarea antes de cerrar.');
                }
                $ticket->update(['closed_at' => now(), 'status' => 'Closed']);
            })(),

            // Reject
            'reason' => $ticket->update([
                'status' => 'Rejected',
                'description' => $ticket->description.'

[Rejected] '.$request->string('reason')->toString(),
            ]),

            default => abort(422, "Campo no permitido: {$field}"),
        };

        $this->hxNotify('Guardado');

        return $this->hxResponse();
    }
}
