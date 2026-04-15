<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters;

use App\Domain\Printing\Models\Wo;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DetailAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Wo $wo): Response
    {
        $items = $wo->items()->get()->map(fn (object $item): array => [
            'id' => $item->code,
            'description' => stripslashes((string) $item->description),
            'fuc' => (string) $item->fuc,
            'qty' => (int) $item->qty,
            'print_val' => 0,
        ])->values()->all();

        return $this->hxView('printing::detail', [
            'wo' => $wo,
            'items' => $items,
            'isEsId' => ! empty($wo->es_id),
        ]);
    }

    public function asController(Wo $wo): Response
    {
        return $this->handle($wo);
    }
}
