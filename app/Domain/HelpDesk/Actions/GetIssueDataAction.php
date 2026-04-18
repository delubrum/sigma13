<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Actions;

use App\Domain\HelpDesk\Data\TableData;
use App\Domain\HelpDesk\Models\Issue;
use App\Domain\HelpDesk\Queries\IssueTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetIssueDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = IssueTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()->map(static fn (Issue $issue): TableData => TableData::fromModel($issue))->all()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
