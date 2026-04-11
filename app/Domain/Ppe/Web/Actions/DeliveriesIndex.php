<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Actions;

use App\Domain\Ppe\Data\DeliveryTable;
use App\Domain\Ppe\Queries\PpeDeliveryTableQuery;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DeliveriesIndex
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'ppe/deliveries',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'PPE Deliveries',
            subtitle: 'Registro de entregas de implementación OHS',
            icon: 'ri-truck-line',
            newButtonLabel: 'Nueva Entrega',
            modalWidth: '60',
            columns: SchemaGenerator::toColumns(DeliveryTable::class),
            formFields: [],
            tabs: [],
            options: []
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $paginator = PpeDeliveryTableQuery::make()->apply($filters, $sorts)->paginate($request->integer('page', 1), $request->integer('size', 15));

        return response()->json([
            'data'      => $paginator->getCollection()->map(fn($d) => new DeliveryTable(
                id:       $d->id,
                date:     $d->created_at?->format('Y-m-d') ?? '',
                name:     $d->name,
                type:     $d->kind,
                employee: $d->employee?->name ?? 'Unknown',
                area:     $d->employee?->profile_name ?? 'N/A', // Assuming relation or field
                user:     $d->user?->username ?? 'Unknown',
                notes:    $d->notes,
            ))->toArray(),
            'last_page' => $paginator->lastPage(),
            'last_row'  => $paginator->total(),
        ]);
    }
}
