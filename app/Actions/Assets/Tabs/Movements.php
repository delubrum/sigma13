<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\AssetEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

final class Movements implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
            title: 'Historial de Movimientos',
            subtitle: '',
            icon: 'ri-arrow-left-right-line',
            newButtonLabel: 'Asignar Activo',
            columns: [
                [
                    'title' => 'Tipo', 
                    'field' => 'kind', 
                    'width' => 120,
                    'formatter' => 'html',
                ],
                ['title' => 'Fecha', 'field' => 'date', 'width' => 130],
                ['title' => 'Responsable', 'field' => 'assignee', 'width' => 250],
                ['title' => 'Hardware Provisto', 'field' => 'hardware', 'formatter' => 'textarea'],
                ['title' => 'Software Instalado', 'field' => 'software', 'formatter' => 'textarea'],
                ['title' => 'Acta', 'field' => 'minute', 'formatter' => 'html', 'width' => 100, 'hozAlign' => 'center'],
            ],
            formFields: [
                new \App\Data\Shared\Field(name: 'employee_id', label: 'Empleado', required: true),
                new \App\Data\Shared\Field(name: 'hardware', label: 'Hardware', required: false),
                new \App\Data\Shared\Field(name: 'software', label: 'Software', required: false),
            ]
        );
    }

    public function handle(int $id): \Illuminate\Contracts\View\View
    {
        $asset = Asset::findOrFail($id);
        
        $customRoute = null;
        $btnClass = null;
        $btnLabel = null;

        if ($asset->status === 'available') {
            $customRoute = route('assets.assignments.create', $id);
            $btnLabel = 'Asignar Activo';
        } elseif ($asset->status === 'assigned') {
            $customRoute = route('assets.returns.create', $id);
            $btnLabel = 'Devolver Activo';
            $btnClass = 'px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider flex items-center space-x-1.5 transition-all outline-none bg-red-600 text-white hover:bg-red-700';
        }

        return view('components.tab-index', [
            'config' => $this->config(),
            'parentId' => $id,
            'route' => 'assets.movements',
            'customCreateRoute' => $customRoute,
            'newButtonClass' => $btnClass,
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }

    public function asData(Request $request, int $id): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 10));
        $offset = ($page - 1) * $size;

        $query = AssetEvent::query()
            ->where('asset_id', $id)
            ->whereIn('kind', ['assignment', 'return'])
            ->with(['employee']);

        $total = $query->count();
        
        $events = $query->orderByDesc('id')
            ->offset($offset)
            ->limit($size)
            ->get()
            ->map(function (AssetEvent $event, int $index) use ($offset): \App\Data\Assets\Tabs\MovementTable {
                $isLatest = ($offset === 0 && $index === 0);
                return \App\Data\Assets\Tabs\MovementTable::fromModel($event, $isLatest);
            });

        return response()->json([
            'data' => $events->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }
}
