<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class AutomationTable extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $activity,
        public readonly string $frequency,
        public readonly string $last_performed_at,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Actividad', field: 'activity', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Frecuencia', field: 'frequency', width: 150, headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Última Ejecución', field: 'last_performed_at', width: 150, headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
        ];
    }

    public static function fromModel(MntPreventiveForm $task): self
    {
        $last = $task->last_performed_at;
        $lastDate = $last instanceof \DateTimeInterface
            ? $last->format('d/m/Y')
            : (is_string($last) ? $last : '---');

        return new self(
            id: $task->id,
            activity: $task->activity ?? '---',
            frequency: $task->frequency ?? '---',
            last_performed_at: $lastDate,
        );
    }
}
