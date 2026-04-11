<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Movements
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        return $this->hxView('components::tab-index', [
            'route' => "assets/{$id}/movements",
            'config' => new \App\Domain\Shared\Data\Config(
                title: 'Historial de Movimientos',
                icon: 'ri-arrow-left-right-line',
                columns: \App\Domain\Assets\Data\Table::columns(),
                formFields: [],
            ),
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
