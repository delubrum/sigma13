<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Preoperational\Actions\SaveAnswersAction;
use App\Domain\Preoperational\Models\Preoperational;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class FinalizeAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    /** @param array<string, mixed> $data */
    public function handle(int $id, array $data): Response
    {
        if ($id === 0) {
            return response()->noContent();
        }

        SaveAnswersAction::run($id, $data);

        $preop = Preoperational::find($id);
        if (! $preop) {
            return response()->noContent();
        }

        DB::transaction(function () use ($preop): void {
            // Processing logic from legacy controller is partially integrated here
            // Note: Preventives and Correctives complex logic should ideally be in an Action
            // processTickets($id);

            $preop->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);
        });

        $headers = json_encode([
            'showMessage' => [
                'type' => 'success',
                'message' => 'Preoperacional Finalizado',
                'close' => 'closeNewModal',
            ],
            'refreshDatatable' => true,
        ]);

        return response('')->header('HX-Trigger', $headers ?: '{}');
    }

    public function asController(Request $request): Response
    {
        return $this->handle((int) $request->input('id', 0), $request->all());
    }
}
