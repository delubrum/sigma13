<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Tabs;

use App\Contracts\HasModule;
use App\Domain\Assets\Data\Tabs\MaintenanceTable;
use App\Domain\Maintenance\Models\Mnt;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Maintenances implements HasModule
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            title: 'Mantenimientos Correctivos',
            subtitle: '',
            icon: 'ri-tools-line',
            newButtonLabel: 'Nuevo Correctivo',
            columns: MaintenanceTable::columns(),
            formFields: [
                new Field(name: 'description', label: 'Descripción de la falla', required: true),
                new Field(name: 'subtype', label: 'Tipo', required: false, placeholder: 'Hardware, Software...'),
            ],
        );
    }

    public function handle(int $id): View
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
        $size = max(1, $request->integer('size', 10));
        $page = max(1, $request->integer('page', 1));

        $paginator = Mnt::query()
            ->where('asset_id', $id)
            ->with(['user'])
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (Mnt $mnt): MaintenanceTable => MaintenanceTable::fromModel($mnt))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
