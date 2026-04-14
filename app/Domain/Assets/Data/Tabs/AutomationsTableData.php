<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Shared\Data\Column;
use Carbon\Carbon;
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

    /** @param mixed $row stdClass (from DB::table) or any object with matching properties */
    public static function fromModel(mixed $row): self
    {
        $lastDate = isset($row->last_performed_at) && $row->last_performed_at !== null
            ? Carbon::parse((string) $row->last_performed_at)->format('d/m/Y')
            : '---';

        return new self(
            id:                (int) $row->id,
            activity:          $row->activity ?? '---',
            frequency:         $row->frequency ?? '---',
            last_performed_at: $lastDate,
        );
    }
}
