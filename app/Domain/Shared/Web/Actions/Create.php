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
            $indexAction = "App\\Domain\\{$domain}\\Web\\Adapters\\IndexAdapter";
        }

        if (! class_exists($indexAction)) {
            $indexAction = "App\\Domain\\{$domain}\\Web\\Adapters\\{$domain}IndexAdapter";
        }

        if (! class_exists($indexAction)) {
            abort(404, "Módulo {$domain} no encontrado.");
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
            $singular = Str::singular($domain);
            $modelClass = "App\\Domain\\{$domain}\\Models\\".$singular;
            $model = $modelClass::findOrFail($id);

            $dtoClass = "App\\Domain\\{$domain}\\Data\\UpsertData";

            if (! class_exists($dtoClass)) {
                $dtoClass = "App\\Domain\\{$domain}\\Data\\{$singular}UpsertData";
            }

            if (class_exists($dtoClass)) {
                $data = $dtoClass::from($model);
            }
        }

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => ($id ? 'Edit ' : 'New ').$config->title,
            'subtitle' => $config->subtitle ?: 'Registro',
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('shared::components.new-modal', [
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
