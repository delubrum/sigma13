<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Actions;

use App\Domain\Extrusion\Data\TableData;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class AiDataAction
{
    use AsAction;

    /**
     * @param  array<string,mixed> $params
     * @return PaginatedResult<TableData>
     */
    public function handle(array $params, int $page, int $size): PaginatedResult
    {
        $tolerance = (float) ($params['tolerance'] ?? 0.05);
        $query     = DB::table('matrices');

        // Numeric dimension filters
        foreach (['b', 'h', 'e1', 'e2'] as $d) {
            if (isset($params["{$d}_min"], $params["{$d}_max"])) {
                $query->whereBetween($d, [(float) $params["{$d}_min"], (float) $params["{$d}_max"]]);
            } elseif (isset($params["{$d}_gt"])) {
                $query->where($d, '>', (float) $params["{$d}_gt"]);
            } elseif (isset($params["{$d}_lt"])) {
                $query->where($d, '<', (float) $params["{$d}_lt"]);
            } elseif (isset($params[$d])) {
                $val = (float) $params[$d];
                $query->whereBetween($d, [$val - $tolerance, $val + $tolerance]);
            }
        }

        // Category — exact first, ILIKE fallback
        if (! blank($params['category'] ?? null)) {
            $cat   = (string) $params['category'];
            $exact = (clone $query)->where('category_id', $cat)->count();
            $exact > 0
                ? $query->where('category_id', $cat)
                : $query->where('category_id', 'ilike', "%{$cat}%");
        }

        // Company — exact first, ILIKE fallback
        if (! blank($params['company'] ?? null)) {
            $company = (string) $params['company'];
            $exact   = (clone $query)->where('company_id', $company)->count();
            $exact > 0
                ? $query->where('company_id', $company)
                : $query->where('company_id', 'ilike', "%{$company}%");
        }

        $query->orderBy('id', 'desc');

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return new PaginatedResult(
            items: array_map(
                static fn ($row) => TableData::fromModel($row),
                $paginator->items()
            ),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }
}
