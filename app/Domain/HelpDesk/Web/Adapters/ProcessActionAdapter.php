<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Web\Adapters;

use App\Domain\HelpDesk\Models\Issue;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class ProcessActionAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, string $action, ?string $reason = null): JsonResponse
    {
        $issue = Issue::findOrFail($id);

        match ($action) {
            'attend' => $issue->update([
                'status'     => 'Attended',
                'ended_at'   => now(),
            ]),
            'close' => $issue->update([
                'status'    => 'Closed',
                'closed_at' => now(),
            ]),
            'reject' => $issue->update([
                'status' => 'Rejected',
                'reason' => $reason,
            ]),
            default => abort(400, 'Acción no válida'),
        };

        $label = match ($action) {
            'attend' => 'atendido',
            'close'  => 'cerrado',
            'reject' => 'rechazado',
            default  => $action,
        };

        $this->hxNotify("Ticket {$label} correctamente");
        $this->hxRefreshTables(['dt_helpdesk']);
        $this->hxRefresh(['#sidebar-summary']);

        return $this->hxResponse();
    }

    public function asController(Request $request, int $id, string $action = ''): JsonResponse
    {
        $resolved = $action !== '' ? $action : (string) $request->route('action', '');

        return $this->handle($id, $resolved, $request->string('prompt_value')->value() ?: null);
    }
}
