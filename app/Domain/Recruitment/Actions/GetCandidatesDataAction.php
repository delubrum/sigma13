<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Data\CandidateTableData;
use App\Domain\Recruitment\Queries\CandidateTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetCandidatesDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<CandidateTableData>
     */
    public function handle(int $recruitmentId, array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = CandidateTableQuery::make()
            ->apply($filters, $sorts, $recruitmentId)
            ->paginate($page, $size);

        /** @var list<CandidateTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (mixed $row): CandidateTableData => CandidateTableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
