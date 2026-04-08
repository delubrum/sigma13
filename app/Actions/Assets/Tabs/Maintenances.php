<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\Mnt;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

final class Maintenances implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
            title: 'Mantenimientos Correctivos',
            subtitle: '',
            icon: 'ri-tools-line',
            newButtonLabel: 'Nuevo Correctivo',
            columns: [
                ['title' => 'Tipo', 'field' => 'type', 'width' => 120],
                ['title' => 'Fecha', 'field' => 'date', 'width' => 130],
                ['title' => 'Solicitante', 'field' => 'user', 'width' => 200],
                ['title' => 'Descripción', 'field' => 'description'],
                ['title' => 'Cerrado', 'field' => 'closed', 'width' => 130],
                ['title' => 'Estado', 'field' => 'status', 'width' => 100],
                ['title' => 'Calificación', 'field' => 'rating', 'width' => 100],
            ],
            formFields: [
                new \App\Data\Shared\Field(name: 'description', label: 'Descripción de la falla', required: true),
                new \App\Data\Shared\Field(name: 'subtype', label: 'Tipo', required: false, placeholder: 'Hardware, Software...'),
            ]
        );
    }

    public function handle(int $id): \Illuminate\Contracts\View\View
    {
        return view('components.tab-index', [
            'config' => $this->config(),
            'parentId' => $id,
            'route' => 'assets.maintenances',
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

        $query = Mnt::query()
            ->where('asset_id', $id)
            ->with(['user', 'asset']);

        $total = $query->count();
        
        $maintenances = $query->orderByDesc('id')
            ->offset($offset)
            ->limit($size)
            ->get()
            ->map(fn (Mnt $mnt): \App\Data\Assets\Tabs\MaintenanceTable => \App\Data\Assets\Tabs\MaintenanceTable::fromModel($mnt));

        return response()->json([
            'data' => $maintenances->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }
}
