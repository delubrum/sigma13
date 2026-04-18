<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Contracts\HasDetail;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Support\DomainResolver;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Sidebar
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, int $id): Response
    {
        $domain      = Str::studly($route);
        $indexAction = "App\\Domain\\{$domain}\\Web\\Adapters\\IndexAdapter";

        if (! class_exists($indexAction)) {
            abort(404);
        }

        /** @var HasDetail $instance */
        $instance    = resolve($indexAction);
        $sidebarData = $instance->sidebarData($id);

        return $this->hxView("{$route}::sidebar", ['data' => $sidebarData]);
    }

    public function asController(Request $request, string $route, string $id): Response
    {
        return $this->handle($route, (int) $id);
    }
}
