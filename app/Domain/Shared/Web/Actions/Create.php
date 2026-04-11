<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Contracts\HasModule;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, ?int $id = null): View
    {
        $domain = Str::studly($route);
        $indexAction = "App\\Domain\\{$domain}\\Actions\\Index";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        /** @var HasModule $instance */
        $instance = resolve($indexAction);
        $config = $instance->config();

        if ($config->formFields === []) {
            abort(404, 'Este módulo no permite creación de registros.');
        }

        $model = null;
        $data = [];

        if ($id) {
            $modelClass = "App\\Domain\\{$domain}\\Models\\" . Str::singular($domain);
            $model = $modelClass::findOrFail($id);

            $dtoClass = "App\\Domain\\{$domain}\\Data\\UpsertData";
            if (class_exists($dtoClass)) {
                $data = $dtoClass::from($model);
            }
        }

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => ($id ? 'Edit ' : 'New ') . $config->title,
            'subtitle' => $config->subtitle ?: 'Registro',
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route' => $route,
            'config' => $config,
            'data' => $data,
        ]);
    }

    public function asController(Request $request, string $route, ?int $id = null): Response
    {
        return $this->hxView($this->handle($route, $id ? (int) $id : null));
    }
}
