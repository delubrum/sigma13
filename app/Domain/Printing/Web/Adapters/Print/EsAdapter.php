<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters\Print;

use App\Domain\Printing\Actions\FetchEsDataAction;
use App\Domain\Printing\Models\Wo;
use App\Domain\Printing\Models\WoItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

final class EsAdapter
{
    use AsAction;

    public function handle(Request $request, Wo $wo): Response
    {
        /** @var list<string> $itemCodes */
        $itemCodes = array_filter((array) $request->input('id', []));
        /** @var list<string> $marcas */
        $marcas = (array) $request->input('marca', []);
        /** @var list<string> $esIds */
        $esIds = (array) $request->input('esid', []);

        if (empty($itemCodes)) {
            abort(400, 'No items selected.');
        }

        /** @var array<string, WoItem> $itemsMap */
        $itemsMap = $wo->items()
            ->whereIn('code', $itemCodes)
            ->get()
            ->keyBy('code')
            ->all();

        $qrUrl = Storage::disk('public')->exists("print/{$wo->code}/qr.png")
            ? Storage::disk('public')->url("print/{$wo->code}/qr.png")
            : null;

        // Fetch ES data per unique ES ID
        $esData = [];
        foreach (array_unique($esIds) as $esId) {
            try {
                $esData[$esId] = FetchEsDataAction::run($esId);
            } catch (Throwable) {
                $esData[$esId] = [];
            }
        }

        // Map marca → consecutivos + orden names from ES API response
        $consecutivos = [];
        $ordenNames   = [];
        $noDataMarcas = [];

        for ($i = 0; $i < count($itemCodes); $i++) {
            $marca   = $marcas[$i] ?? '';
            $esId    = $esIds[$i] ?? '';
            $apiRows = $esData[$esId] ?? [];

            $found = false;
            foreach ($apiRows as $row) {
                if (($row['marca'] ?? '') === $marca) {
                    $found = true;
                    if (isset($row['consecutivo'])) {
                        $consecutivos[$marca][] = $row['consecutivo'];
                    }
                    if (isset($row['nombreorden'])) {
                        $ordenNames[$marca][] = $row['nombreorden'];
                    }
                }
            }

            if (! $found && ! isset($consecutivos[$marca])) {
                $consecutivos[$marca] = ['NO_DATA'];
                $ordenNames[$marca]   = ['SIN DATOS ES'];
                $noDataMarcas[]       = $marca;
            }
        }

        return response(
            view('printing::print.es', [
                'wo'           => $wo,
                'itemCodes'    => $itemCodes,
                'marcas'       => $marcas,
                'itemsMap'     => $itemsMap,
                'consecutivos' => $consecutivos,
                'ordenNames'   => $ordenNames,
                'noDataMarcas' => array_unique($noDataMarcas),
                'qrUrl'        => $qrUrl,
            ])->render()
        )->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function asController(Request $request, Wo $wo): Response
    {
        return $this->handle($request, $wo);
    }
}
