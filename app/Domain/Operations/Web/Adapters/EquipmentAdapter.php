<?php

declare(strict_types=1);

namespace App\Domain\Operations\Web\Adapters;

use App\Domain\Operations\Actions\GetEquipmentData;
use App\Domain\Operations\Actions\GetEquipmentDeliveries;
use App\Domain\Operations\Actions\GetEquipmentStock;
use App\Domain\Operations\Data\EquipmentDeliveryTableData;
use App\Domain\Operations\Data\EquipmentStockTableData;
use App\Domain\Operations\Data\EquipmentTableData;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class EquipmentAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Request $request): Response
    {
        $tab = $request->get('tab', 'db');

        $config = match ($tab) {
            'deliveries' => new Config(
                title:   'Equipment Deliveries',
                subtitle: 'Historial de equipos entregados a empleados',
                icon:    'ri-tools-line',
                columns: [],
            ),
            'stock' => new Config(
                title:   'Equipment Stock',
                subtitle: 'Niveles actuales y entradas de equipos',
                icon:    'ri-archive-line',
                columns: [],
            ),
            default => new Config(
                title:   'Equipment Catalogue',
                subtitle: 'Gestión de catálogo de equipos',
                icon:    'ri-settings-5-line',
                columns: [],
            ),
        };

        return $this->hxView('operations::equipment.index', [
            'config' => $config,
            'tab'    => $tab,
        ]);
    }

    public function asController(Request $request): Response
    {
        return $this->handle($request);
    }

    public function asData(Request $request): JsonResponse
    {
        $tab     = $request->get('tab', 'db');
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();
        $page    = $request->integer('page', 1);
        $size    = $request->integer('size', 15);

        $result = match ($tab) {
            'deliveries' => GetEquipmentDeliveries::run($filters, $sorts, $page, $size),
            'stock'      => GetEquipmentStock::run($filters, $sorts, $page, $size),
            default      => GetEquipmentData::run($filters, $sorts, $page, $size),
        };

        return response()->json($result);
    }
}
