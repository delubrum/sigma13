<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Contracts\HasPatch;
use App\Support\DomainResolver;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class Patch
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, int $id, Request $request): JsonResponse
    {
        $domain    = DomainResolver::fromRoute($route);
        $indexClass = "App\\Domain\\{$domain}\\Web\\Adapters\\IndexAdapter";

        if (! class_exists($indexClass)) {
            abort(404);
        }

        /** @var HasPatch $index */
        $index = resolve($indexClass);

        if (! $index instanceof HasPatch) {
            abort(404);
        }

        $field  = $request->string('field')->toString();
        $config = $index->patchConfig($id);

        if (! array_key_exists($field, $config['fields'])) {
            abort(403, "Field '{$field}' is not patchable.");
        }

        DB::table($config['table'])->where('id', $id)->update([$field => $request->input($field)]);

        $divIds = $config['fields'][$field];
        if ($divIds !== []) {
            $this->hxRefresh($divIds);
        }

        return $this->hxNotify('Actualizado correctamente')
            ->hxRefreshTables(["dt_{$route}"])
            ->hxResponse();
    }

    public function asController(Request $request, string $route, int $id): JsonResponse
    {
        return $this->handle($route, $id, $request);
    }
}
