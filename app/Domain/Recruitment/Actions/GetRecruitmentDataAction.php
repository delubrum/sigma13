<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Data\TableData;
use App\Domain\Recruitment\Queries\RecruitmentTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetRecruitmentDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = RecruitmentTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (mixed $row): TableData => TableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
