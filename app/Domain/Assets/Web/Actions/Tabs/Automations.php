<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Data\Tabs\AutomationTable;
use App\Domain\Assets\Models\Asset;
use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automations
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Automations',
            subtitle: '',
            icon: 'ri-settings-4-line',
            newButtonLabel: 'Nueva Tarea',
            columns: AutomationTable::columns(),
            formFields: [
                new Field(name: 'activity', label: 'Actividad', required: true),
                new Field(name: 'frequency', label: 'Frecuencia', required: true),
            ],
        );
    }

    public function handle(Asset $asset): Response
    {
        return $this->hxView('components::tab-index', [
            'config' => $this->config(),
            'parentId' => $asset->id,
            'route' => 'assets.automations',
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

        $paginator = MntPreventiveForm::query()
            ->where('asset_id', $asset->id)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (MntPreventiveForm $task): AutomationTable => AutomationTable::fromModel($task))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
