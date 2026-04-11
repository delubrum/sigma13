<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automation
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Configurar Automatización',
            subtitle: 'Automatiza tareas sobre este activo',
            icon: 'ri-settings-4-line',
            newButtonLabel: 'Nueva Automatización',
            modalWidth: '50%',
            columns: [],
            formFields: [],
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();
        return view('components.new-modal', [
            'route' => "assets/{$id}/automations",
            'config' => $config,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->hxView($this->handle($id));
    }
}
