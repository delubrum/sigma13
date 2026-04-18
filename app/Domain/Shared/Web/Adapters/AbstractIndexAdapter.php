<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class AbstractIndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    abstract public function config(): Config;

    abstract protected function route(): string;

    /** @return PaginatedResult<mixed> */
    abstract protected function getData(array $filters, array $sorts, int $page, int $size): PaginatedResult;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'         => $this->route(),
            'config'        => $this->config(),
            'hasDetail'     => $this instanceof \App\Contracts\HasDetail,
            'hasEditDetail' => $this instanceof \App\Contracts\HasEditDetail,
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $result = $this->getData(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
