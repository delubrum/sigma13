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

        #[Column(title: 'Actividad')]
        public readonly string $activity,

        #[Column(title: 'Frecuencia', width: 150)]
        public readonly string $frequency,

        #[Column(title: 'Última Ejecución', width: 150)]
        public readonly string $last_performed_at,
    ) {}

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
