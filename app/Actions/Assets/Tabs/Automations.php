<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\MntPreventiveForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automations implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
            title: 'Automations',
            subtitle: '',
            icon: 'ri-settings-4-line',
            newButtonLabel: 'Nueva Tarea',
            columns: [
                ['title' => 'Actividad', 'field' => 'activity'],
                ['title' => 'Frecuencia', 'field' => 'frequency', 'width' => 150],
                ['title' => 'Última Ejecución', 'field' => 'last_performed_at', 'width' => 150],
            ],
            formFields: [
                new \App\Data\Shared\Field(name: 'activity', label: 'Actividad', required: true),
                new \App\Data\Shared\Field(name: 'frequency', label: 'Frecuencia', required: true),
            ]
        );
    }

    public function handle(int $id): \Illuminate\Contracts\View\View
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
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 10));
        $offset = ($page - 1) * $size;

        $query = MntPreventiveForm::query()
            ->where('asset_id', $id);

        $total = $query->count();
        
        $automations = $query->orderByDesc('id')
            ->offset($offset)
            ->limit($size)
            ->get()
            ->map(fn (MntPreventiveForm $task): \App\Data\Assets\Tabs\AutomationTable => \App\Data\Assets\Tabs\AutomationTable::fromModel($task));

        return response()->json([
            'data' => $automations->values()->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }
}
