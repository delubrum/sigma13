<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Abstract base for tab adapters that render a sub-table (components::tab-index).
 *
 * Concrete classes must implement:
 *  - tabConfig()  → column definitions, title, icon, optional formFields
 *  - tabRoute()   → named route prefix (e.g. 'assets.movements')
 *  - tabData()    → paginated data from a Core Action
 *
 * Larastan: all method bodies here reference only concrete types;
 * abstract return types are enforced by the inheritance chain.
 */
abstract class SubTableAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    abstract protected function tabConfig(): Config;

    /** Named route prefix — blade appends '.data' for ajaxURL */
    abstract protected function tabRoute(): string;

    /**
     * @return PaginatedResult<\Spatie\LaravelData\Data>
     */
    abstract protected function tabData(int $parentId, int $page, int $size): PaginatedResult;

    final public function handle(int $id): Response
    {
        return $this->hxView('components::tab-index', [
            'config'   => $this->tabConfig(),
            'parentId' => $id,
            'route'    => $this->tabRoute(),
        ]);
    }

    final public function asController(int $id): Response
    {
        return $this->handle($id);
    }

    final public function asData(Request $request, int $id): JsonResponse
    {
        $result = $this->tabData(
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
