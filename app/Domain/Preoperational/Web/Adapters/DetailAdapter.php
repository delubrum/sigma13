<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Preoperational\Models\Preoperational;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DetailAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $preop = Preoperational::with(['user', 'vehicle', 'items.question'])
            ->find($id);

        if (! $preop) {
            return response('Reporte no encontrado.', 404);
        }

        $items_by_category = [];
        foreach ($preop->items as $item) {
            $cat = $item->question->category ?? 'Sin Categoría';
            $items_by_category[$cat][] = (object) [
                'question' => $item->question->question,
                'answer' => $item->answer,
                'url' => $item->url,
                'obs' => $item->obs,
                'ticket_ids' => $item->ticket_ids,
            ];
        }

        $idObj = (object) [
            'idd' => $preop->id,
            'created_at' => $preop->created_at,
            'user' => $preop->user->username ?? '—',
            'hostname' => $preop->vehicle->hostname ?? '—',
            'brand' => $preop->vehicle->brand ?? '—',
            'serial' => $preop->vehicle->serial ?? '—',
        ];

        return $this->hxView('preoperational::detail', [
            'id' => $idObj,
            'items_by_category' => $items_by_category,
        ]);
    }

    public function asController(Request $request): Response
    {
        return $this->handle($request->integer('id'));
    }
}
