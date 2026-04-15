<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters\Print;

use App\Domain\Printing\Models\Wo;
use App\Domain\Printing\Models\WoItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class EsmAdapter
{
    use AsAction;

    public function handle(Request $request, Wo $wo): Response
    {
        /** @var list<string> $itemCodes */
        $itemCodes = array_filter((array) $request->input('id', []));
        /** @var list<int> $quantities */
        $quantities = array_map(intval(...), (array) $request->input('val', []));

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

        $labels = [];
        foreach ($itemCodes as $idx => $code) {
            $item = $itemsMap[$code] ?? null;
            if (! $item) {
                continue;
            }
            $qty = $quantities[$idx] ?? 1;
            for ($i = 0; $i < $qty; $i++) {
                $labels[] = $item;
            }
        }

        return response(
            view('printing::print.esm', [
                'wo' => $wo,
                'labels' => $labels,
                'qrUrl' => $qrUrl,
            ])->render()
        )->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function asController(Request $request, Wo $wo): Response
    {
        return $this->handle($request, $wo);
    }
}
