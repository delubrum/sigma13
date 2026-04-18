<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Adapters;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

/**
 * Base for tab adapters that render a paginated sub-table (components::tab-index).
 *
 * @template T of Data
 */
abstract class AbstractSubTableAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    abstract public function config(): Config;

    abstract protected function route(): string;

    /** @return PaginatedResult<T> */
    abstract protected function getData(int $parentId, int $page, int $size): PaginatedResult;

    final public function handle(int $id): Response
    {
        return $this->hxView('components::tab-index', [
            'config'   => $this->configForParent($id),
            'parentId' => $id,
            'route'    => $this->route(),
        ]);
    }

    public function configForParent(int $parentId): Config
    {
        return $this->config();
    }

    final public function asController(int $id): Response
    {
        return $this->handle($id);
    }

    final public function asData(Request $request, int $id): JsonResponse
    {
        $result = $this->getData(
            parentId: $id,
            page:     $request->integer('page', 1),
            size:     $request->integer('size', 25),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
