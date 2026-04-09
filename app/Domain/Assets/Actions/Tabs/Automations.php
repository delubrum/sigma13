<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Tabs;

use App\Contracts\HasModule;
use App\Domain\Assets\Data\Tabs\AutomationTable;
use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automations implements HasModule
{
    use AsAction;

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

    public function handle(int $id): View
    {
        return view('components.tab-index', [
            'config' => $this->config(),
            'parentId' => $id,
            'route' => 'assets.automations',
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

        $paginator = MntPreventiveForm::query()
            ->where('asset_id', $id)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (MntPreventiveForm $task): AutomationTable => AutomationTable::fromModel($task))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
