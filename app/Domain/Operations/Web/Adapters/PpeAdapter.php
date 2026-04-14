<?php

declare(strict_types=1);

namespace App\Domain\Operations\Web\Adapters;

use App\Domain\Operations\Actions\GetPpeData;
use App\Domain\Operations\Actions\GetPpeDeliveries;
use App\Domain\Operations\Actions\GetPpeStock;
use App\Domain\Operations\Data\PpeDeliveryTableData;
use App\Domain\Operations\Data\PpeStockTableData;
use App\Domain\Operations\Data\PpeTableData;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PpeAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Request $request): Response
    {
        $tab = $request->get('tab', 'db');

        $config = match ($tab) {
            'deliveries' => new Config(
                title:   'PPE Deliveries',
                subtitle: 'Historial de EPP entregado a empleados',
                icon:    'ri-truck-line',
                columns: [],
            ),
            'stock' => new Config(
                title:   'PPE Entries',
                subtitle: 'Niveles actuales y entradas de stock',
                icon:    'ri-inventory-line',
                columns: [],
            ),
            default => new Config(
                title:   'PPE Admin',
                subtitle: 'Gestión de catálogo y punto de pedido',
                icon:    'ri-admin-line',
                columns: [],
            ),
        };

        return $this->hxView('operations::ppe.index', [
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
            'deliveries' => GetPpeDeliveries::run($filters, $sorts, $page, $size),
            'stock'      => GetPpeStock::run($filters, $sorts, $page, $size),
            default      => GetPpeData::run($filters, $sorts, $page, $size),
        };

        return response()->json($result);
    }
}
