<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Support\DomainResolver;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, string $id): View
    {
        $domain = DomainResolver::fromRoute($route);
        $indexAction = "App\\Domain\\{$domain}\\Web\\Adapters\\IndexAdapter";

        if (! class_exists($indexAction)) {
            abort(404, "Módulo {$domain} no encontrado.");
        }

        /** @var HasModule $instance */
        $instance = resolve($indexAction);
        $config = $instance->config();

        $sidebarView = null;
        $sidebarData = null;

        if ($instance instanceof HasDetail) {
            $sidebarView = "{$route}::sidebar";
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

        return view('shared::components.detail-modal', [
            'route' => $route,
            'config' => $config,
            'id' => (int) $id,
            'sidebarView' => $sidebarView,
            'sidebarData' => $sidebarData,
            'tabs' => $tabs,
            'defaultTab' => $defaultTab,
        ]);
    }

    public function asController(Request $request, string $route, string $id): Response
    {
        return $this->hxView($this->handle($route, $id));
    }
}
