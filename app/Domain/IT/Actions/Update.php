<?php

declare(strict_types=1);

namespace App\Domain\IT\Actions;

use App\Domain\IT\Models\It;
use App\Domain\IT\Models\ItItem;
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
        $ticket = It::findOrFail($id);

        match ($field) {
            'asset_id' => $ticket->update(['asset_id' => $request->input('value')]),

            'priority' => $ticket->update(['priority' => $request->string('value')->toString()]),

            'sgc' => $ticket->update(['sgc' => $request->string('value')->toString()]),

            'assignee_id' => $ticket->update(['assignee_id' => $request->input('value')]),

            // Attend: requires at least one task
            'ended_at' => (function () use ($ticket): void {
                if (! ItItem::where('it_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Add at least one task before attending.');
                }
                $ticket->update(['ended_at' => now(), 'status' => 'Attended']);
            })(),

            // Close: requires attended
            'closed_at' => (function () use ($ticket): void {
                if (! ItItem::where('it_id', $ticket->id)->exists()) {
                    throw new \RuntimeException('Add at least one task before closing.');
                }
                $ticket->update(['closed_at' => now(), 'status' => 'Closed']);
            })(),

            // Reject with reason
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
