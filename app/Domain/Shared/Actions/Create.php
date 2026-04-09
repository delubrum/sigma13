<?php

declare(strict_types=1);

namespace App\Domain\Shared\Actions;

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

    public function handle(string $route): View
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

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle ?: 'Registro',
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route' => $route,
            'config' => $config,
        ]);
    }

    public function asController(Request $request, string $route): Response
    {
        return $this->hxView($this->handle($route));
    }
}
