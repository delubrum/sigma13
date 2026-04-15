<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters\Tabs;

use App\Domain\Maintenance\Data\TaskTableData;
use App\Domain\Maintenance\Data\TaskUpsertData;
use App\Domain\Maintenance\Models\MaintenanceItem;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use Illuminate\Support\Carbon;

/** @extends SubTableAdapter<TaskTableData> */
final class TasksTabAdapter extends SubTableAdapter
{
    #[\Override]
    protected function tabConfig(): Config
    {
        return new Config(
            title: 'Tareas y Tiempos',
            icon: 'ri-time-line',
            subtitle: 'Registro de actividades y tiempo invertido',
            newButtonLabel: 'Nueva Tarea',
            modalWidth: '40',
            columns: SchemaGenerator::toColumns(TaskTableData::class),
            formFields: SchemaGenerator::toFields(TaskUpsertData::class),
        );
    }

    #[\Override]
    protected function tabRoute(): string
    {
        return 'maintenance.tasks';
    }

    #[\Override]
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        $paginator = MaintenanceItem::query()
            ->with('user')
            ->where('mnt_id', $parentId)
            ->orderBy('id', 'desc')
            ->paginate($size, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(function (MaintenanceItem $item): TaskTableData {
            $date = $item->created_at;
            /** @var string $userName */
            $userName = $item->user->username ?? 'Sistema';

            // Check for evidence file
            $directorio = "uploads/mnt/pics/{$item->id}/";
            $files = glob(public_path($directorio).'*');
            $fileLink = null;
            if ($files && count($files) > 0) {
                $fileName = basename($files[0]);
                $url = asset($directorio.$fileName);
                $fileLink = "<a class=\"font-medium text-blue-600 hover:underline inline-flex items-center gap-1\" target=\"_blank\" href=\"{$url}\"><i class=\"ri-file-search-line\"></i></a>";
            }

            return new TaskTableData(
                date: $date instanceof Carbon ? $date->format('Y-m-d H:i') : '-',
                user: $userName,
                complexity: $item->complexity,
                attends: $item->attends,
                duration: (float) $item->duration,
                notes: (string) $item->notes,
                file: $fileLink,
            );
        });

        return new PaginatedResult(
            items: array_values($items->all()),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
