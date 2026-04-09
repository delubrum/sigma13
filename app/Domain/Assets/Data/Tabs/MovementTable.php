<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Column;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class MovementTable extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $asset_id,
        public readonly bool $is_latest,
        public readonly string $kind,
        public readonly string $date,
        public readonly string $assignee,
        public readonly string $hardware,
        public readonly string $software,
        public readonly string $minute,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Tipo', field: 'kind', width: 130, hozAlign: 'center', formatter: 'html'),
            Column::make(title: 'Fecha', field: 'date', width: 140),
            Column::make(title: 'Responsable', field: 'assignee', headerFilter: 'input'),
            Column::make(title: 'Hardware', field: 'hardware'),
            Column::make(title: 'Software', field: 'software'),
            Column::make(title: 'Acta/Notas', field: 'minute', width: 120, hozAlign: 'center', formatter: 'html'),
        ];
    }

    public static function fromModel(AssetEvent $event, bool $isLatest = false): self
    {
        $date = $event->created_at instanceof Carbon
            ? $event->created_at->format('d/m/Y H:i')
            : '---';

        /** @var string|null $hardware */
        $hardware = is_array($event->hardware) ? implode(', ', $event->hardware) : $event->hardware;

        /** @var string|null $software */
        $software = is_array($event->software) ? implode(', ', $event->software) : $event->software;

        $kindHtml = $event->kind === 'assignment'
            ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-500 border border-blue-500/30 w-full block text-center"><i class="ri-arrow-right-line mr-1"></i>Asignación</span>'
            : '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 w-full block text-center"><i class="ri-arrow-left-line mr-1"></i>Devolución</span>';

        return new self(
            id: $event->id,
            asset_id: $event->asset_id,
            is_latest: $isLatest,
            kind: $kindHtml,
            date: $date,
            assignee: $event->employee->name ?? '---',
            hardware: $hardware ?? '---',
            software: $software ?? '---',
            minute: $event->notes
                ? '<span class="text-emerald-600"><i class="ri-file-text-line"></i> Adjunto</span>'
                : '<span class="opacity-30">---</span>',
        );
    }
}
