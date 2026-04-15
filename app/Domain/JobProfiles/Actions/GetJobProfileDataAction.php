<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Actions;

use App\Domain\JobProfiles\Data\TableData;
use App\Domain\JobProfiles\Queries\JobProfileTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetJobProfileDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = JobProfileTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        $items = array_map(
            TableData::fromModel(...),
            $paginator->items()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: (int) $paginator->lastPage(),
            total: (int) $paginator->total(),
        );
    }
}
