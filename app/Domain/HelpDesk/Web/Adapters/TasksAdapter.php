<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Web\Adapters;

use App\Domain\HelpDesk\Data\TaskTableData;
use App\Domain\HelpDesk\Data\TaskUpsertData;
use App\Domain\HelpDesk\Models\Issue;
use App\Domain\HelpDesk\Models\IssueItem;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Adapters\AbstractSubTableAdapter;
use Illuminate\Support\Facades\Auth;

/** @extends AbstractSubTableAdapter<TaskTableData> */
final class TasksAdapter extends AbstractSubTableAdapter
{
    public function config(): Config
    {
        return new Config(
            title:   'Tareas y Tiempos',
            icon:    'ri-time-line',
            columns: SchemaGenerator::toColumns(TaskTableData::class),
        );
    }

    public function configForParent(int $parentId): Config
    {
        $issue     = Issue::select('assignee_id', 'status')->findOrFail($parentId);
        $terminal  = in_array($issue->status, ['Closed', 'Rejected', 'Rated'], true);
        $canCreate = ! $terminal && ($issue->assignee_id === null || $issue->assignee_id === (int) Auth::id());

        return new Config(
            title:          'Tareas y Tiempos',
            icon:           'ri-time-line',
            newButtonLabel: $canCreate ? 'Nueva Tarea' : '',
            modalWidth:     '40',
            multipart:      true,
            columns:        SchemaGenerator::toColumns(TaskTableData::class),
            formFields:     $canCreate ? SchemaGenerator::toFields(TaskUpsertData::class) : [],
        );
    }

    protected function route(): string
    {
        return 'helpdesk.tasks';
    }

    /** @return PaginatedResult<TaskTableData> */
    protected function getData(int $parentId, int $page, int $size): PaginatedResult
    {
        $paginator = IssueItem::query()
            ->with(['user', 'media'])
            ->where('issue_id', $parentId)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        /** @var list<TaskTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(static fn (IssueItem $item): TaskTableData => TaskTableData::fromModel($item))
                ->all()
        );

        return new PaginatedResult(
            items:    $items,
            lastPage: $paginator->lastPage(),
            total:    $paginator->total(),
        );
    }
}
