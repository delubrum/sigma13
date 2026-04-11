<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Maintenances
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        return $this->hxView('components::tab-index', [
            'route' => "assets/{$id}/maintenances",
            'config' => new \App\Domain\Shared\Data\Config(
                title: 'Mantenimientos Correctivos',
                icon: 'ri-tools-line',
                columns: [], // Add columns if needed
                formFields: [],
            ),
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
