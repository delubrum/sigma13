<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Data\Tabs\MovementTable;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Movements
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Movimientos del Activo',
            subtitle: '',
            icon: 'ri-arrow-left-right-line',
            newButtonLabel: '',
            columns: MovementTable::columns(),
        );
    }

    public function handle(Asset $asset): Response
    {
        $customRoute = '#';
        $customLabel = null;
        $customIcon = null;

        if ($asset->status === 'available') {
            $customRoute = route('assets.assignments.create', $asset);
            $customLabel = 'Asignar Activo';
            $customIcon = 'ri-user-add-line';
        } elseif ($asset->status === 'assigned') {
            $customRoute = route('assets.returns.create', $asset);
            $customLabel = 'Retornar Activo';
            $customIcon = 'ri-reply-line';
        }

        return $this->hxView('components::tab-index', [
            'config' => $this->config(),
            'parentId' => $asset->id,
            'route' => 'assets.movements',
            'customCreateRoute' => $customRoute,
            'customCreateLabel' => $customLabel,
            'customCreateIcon' => $customIcon,
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }

    public function asData(Request $request, Asset $asset): JsonResponse
    {
        $size = max(1, $request->integer('size', 10));
        $page = max(1, $request->integer('page', 1));

        $filters = $request->input('filters', []);

        $query = AssetEvent::query()
            ->where('asset_id', $asset->id)
            ->whereIn('kind', ['assignment', 'return'])
            ->with(['employee', 'media']);

        // Aplicar filtros dinámicos
        foreach ($filters as $filter) {
            $field = $filter['field'] ?? null;
            $value = $filter['value'] ?? null;

            if (!$field || $value === null || $value === '') continue;

            match ($field) {
                'kind' => $query->where('kind', $value),
                'assignee' => $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%$value%")),
                'date' => $query->where('created_at', 'like', "%$value%"),
                'hardware' => $query->where('hardware', 'like', "%$value%"),
                'software' => $query->where('software', 'like', "%$value%"),
                default => null
            };
        }

        $paginator = $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        $isFirstPage = $page === 1;

        return response()->json([
            'data' => $paginator->map(
                fn (AssetEvent $event, int $i): MovementTable => MovementTable::fromModel($event, $isFirstPage && $i === 0)
            )->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
