<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Data\Tabs\MaintenanceTable;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class Maintenances
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(?Asset $asset = null): Config
    {
        return new Config(
            title: $asset ? "Mantenimientos: <span class='opacity-50'>{$asset->serial}</span>" : 'Mantenimientos Correctivos',
            subtitle: $asset?->name ?? 'Gestionar mantenimientos de este activo',
            icon: 'ri-tools-line',
            newButtonLabel: 'Nuevo Correctivo',
            columns: MaintenanceTable::columns(),
            formFields: [
                new Field(name: 'description', label: 'Descripción de la falla', required: true),
                new Field(name: 'subtype', label: 'Tipo', required: false, placeholder: 'Hardware, Software...'),
            ],
        );
    }

    public function handle(Asset $asset): Response
    {
        return $this->hxView('components::tab-index', [
            'config' => $this->config($asset),
            'parentId' => $asset->id,
            'route' => 'assets.maintenances',
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
        $offset = ($page - 1) * $size;

        $query = DB::table('mnt')
            ->where('asset_id', $asset->id)
            ->whereNull('deleted_at')
            ->orderByDesc('id');

        $total = $query->count();
        $rows = $query->offset($offset)->limit($size)->get();

        return response()->json([
            'data' => $rows->map(fn (object $mnt): MaintenanceTable => MaintenanceTable::from((array) $mnt))->values(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }
}
