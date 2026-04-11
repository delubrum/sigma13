<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, ?string $id = null): Response
    {
        $domain = Str::studly($route);
        $singular = Str::singular($domain);
        $indexAction = "App\\Domain\\{$domain}\\Actions\\Index";
        $modelClass = "App\\Domain\\{$domain}\\Models\\{$singular}";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        $instance = resolve($indexAction);
        $config = $instance->config();
        
        $data = $id ? $modelClass::findOrFail($id) : [];

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => ($id ? "Editar " : "Nuevo ") . $config->title,
            'subtitle' => $config->subtitle ?: '',
        ]);

        $this->hxModalWidth($config->modalWidth ?: 'md');

        return $this->hxView('components::new-modal', [
            'route' => $route,
            'config' => $config,
            'data' => $data,
        ]);
    }

    public function asController(Request $request, string $route, ?string $id = null): Response
    {
        return $this->handle($route, $id);
    }
}
