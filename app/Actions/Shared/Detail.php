<?php

declare(strict_types=1);

namespace App\Actions\Shared;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, int $id): View
    {
        $folder = Str::studly($route);
        $indexAction = "App\\Actions\\{$folder}\\Index";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        /** @var HasModule&HasDetail $instance */
        $instance = app($indexAction);
        $config = $instance->config();

        $sidebarView = null;
        $sidebarData = null;

        if ($instance instanceof HasDetail) {
            $sidebarView = "{$route}.sidebar";
            $sidebarData = $instance->sidebarData($id);
        }

        $tabs = $config->tabs;
        $defaultTab = collect($tabs)->first(fn ($t) => $t->default === true);

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle ?: 'Detalle',
        ]);

        $this->hxModalWidth('98%');

        return view('components.detail-modal', [
            'route' => $route,
            'config' => $config,
            'id' => $id,
            'sidebarView' => $sidebarView,
            'sidebarData' => $sidebarData,
            'tabs' => $tabs,
            'defaultTab' => $defaultTab,
        ]);
    }

    public function asController(Request $request, string $route, int $id): Response
    {
        return $this->hxView($this->handle($route, $id));
    }
}
