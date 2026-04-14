<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Shared\Data\Column;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

final class AutomationsTableData extends Data
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

    public static function fromModel(mixed $row): self
    {
        /** @var \stdClass $row */
        $lastDate = isset($row->last_performed_at)
            ? Date::parse((string) $row->last_performed_at)->format('d/m/Y')
            : '---';

        return new self(
            id: (int) ($row->id ?? 0),
            activity: (string) ($row->activity ?? '---'),
            frequency: (string) ($row->frequency ?? '---'),
            last_performed_at: $lastDate,
        );
    }
}
