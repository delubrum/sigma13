<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters\Modals;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class NewDeliveryAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->asGet();
    }

    public function asGet(): Response
    {
        $items = DB::table('epp_db')->orderBy('name')->get(['id', 'name']);

        return $this->hxView('ppe::modals.new-delivery', [
            'items' => $items,
        ]);
    }

    public function asSave(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => ['required', 'integer'],
        ]);

        $signatureBlob = null;
        if (! blank($request->input('signature_base64'))) {
            $raw = (string) $request->input('signature_base64');
            $raw = str_replace('data:image/jpeg;base64,', '', $raw);
            $raw = str_replace(' ', '+', $raw);
            $decoded = base64_decode($raw, true);
            if ($decoded !== false) {
                $signatureBlob = gzcompress($decoded, 9);
            }
        }

        $types = $request->input('type', []);
        $saved = false;

        foreach ($types as $ppeId => $typeValue) {
            if (blank($typeValue)) {
                continue;
            }

            $itemName = DB::table('epp_db')->where('id', (int) $ppeId)->value('name') ?? 'Unknown';

            DB::table('epp')->insert([
                'user_id'      => (int) auth()->id(),
                'employee_id'  => $request->integer('employee_id'),
                'notes'        => $request->input('notes', ''),
                'kind'         => $typeValue,
                'name'         => $itemName,
                'img'          => $signatureBlob,
                'is_optimized' => 1,
                'created_at'   => now(),
            ]);

            $saved = true;
        }

        if (! $saved) {
            return $this->hxNotify('warning', 'Selecciona al menos un EPP con tipo de entrega.')
                ->hxResponse();
        }

        return $this->hxNotify('success', 'Entrega registrada correctamente.')
            ->hxRefreshTables(['dt_ppe-deliveries'])
            ->hxCloseModals(['modal-body'])
            ->hxResponse();
    }
}
