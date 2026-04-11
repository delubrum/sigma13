<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, string $id): Response
    {
        if (! ctype_digit($id) && ! is_int($id)) {
            abort(404, "Identificador inválido: {$id}");
        }

        $domain = Str::studly($route);
        $indexAction = "App\\Domain\\{$domain}\\Actions\\Index";

        if (! class_exists($indexAction)) {
            abort(404, 'Módulo no encontrado.');
        }

        /** @var HasModule&HasDetail $instance */
        $instance = resolve($indexAction);
        $config = $instance->config();

        $sidebarData = null;

        if ($instance instanceof HasDetail) {
            $sidebarData = $instance->sidebarData((int) $id);
        }

        $displayName = null;
        if (is_object($sidebarData)) {
            $displayName = match (true) {
                property_exists($sidebarData, 'name') => $sidebarData->name ?? null,
                property_exists($sidebarData, 'user') => $sidebarData->user ?? null,
                property_exists($sidebarData, 'hostname') => $sidebarData->hostname ?? null,
                property_exists($sidebarData, 'serial') => $sidebarData->serial ?? null,
                default => null,
            };
        }

        $tabs = $config->tabs;
        $defaultTab = collect($tabs)->first(fn ($t): bool => $t->default);

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => "{$config->title} · #{$id}".($displayName ? " · {$displayName}" : ''),
            'subtitle' => $config->subtitle ?: '',
        ]);

        $this->hxModalWidth('98');

        $sidebarView = "{$route}::sidebar";
        if (! view()->exists($sidebarView)) {
            $sidebarView = null;
        }

        return $this->hxView('components::detail-modal', [
            'route' => $route,
            'config' => $config,
            'id' => (int) $id,
            'sidebarData' => $sidebarData,
            'sidebarView' => $sidebarView,
            'tabs' => $tabs,
            'defaultTab' => $defaultTab,
            'displayName' => $displayName,
        ]);
    }

    public function asController(Request $request, string $route, string $id): Response
    {
        return $this->handle($route, $id);
    }
}
