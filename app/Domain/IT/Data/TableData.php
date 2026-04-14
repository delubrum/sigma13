<?php

declare(strict_types=1);

namespace App\Domain\IT\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public string $date,

        #[Column(title: 'Usuario', width: 150, headerFilter: 'input')]
        public string $user,

        #[Column(title: 'Sede', width: 120, headerFilter: 'input')]
        public ?string $facility,

        #[Column(title: 'Activo', width: 200, headerFilter: 'input')]
        public ?string $asset,

        #[Column(title: 'Prioridad', width: 100, headerFilter: 'input')]
        public ?string $priority,

        #[Column(
            title: 'Estado',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values'    => ['Open' => 'Open', 'Started' => 'Started', 'Attended' => 'Attended', 'Closed' => 'Closed', 'Rated' => 'Rated', 'Rejected' => 'Rejected'],
                'clearable' => true,
            ]
        )]
        public string $status,

        #[Column(title: 'Descripción', width: 250, formatter: 'textarea', headerFilter: 'input')]
        public ?string $description,

        #[Column(title: 'Asignado', width: 150, headerFilter: 'input')]
        public ?string $assignee,

        #[Column(title: 'Días', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public int $days,

        #[Column(title: 'Iniciado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $started_at,

        #[Column(title: 'Atendido', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $attended,

        #[Column(title: 'Cerrado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $closed,

        #[Column(title: 'Horas', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public float $time,

        #[Column(title: 'SGC', width: 100, headerFilter: 'input')]
        public ?string $sgc,

        #[Column(title: 'Rating', width: 80, hozAlign: 'center', headerFilter: 'number')]
        public ?int $rating,
    ) {}

    public static function fromModel(object $row): self
    {
        $status = (string) ($row->status ?? 'Open');
        $color  = match ($status) {
            'Open'     => 'border-blue-500 text-blue-500',
            'Started'  => 'border-yellow-500 text-yellow-500',
            'Attended' => 'border-purple-500 text-purple-500',
            'Closed'   => 'border-green-500 text-green-500',
            'Rated'    => 'border-emerald-500 text-emerald-500',
            'Rejected' => 'border-red-500 text-red-500',
            default    => 'border-sigma-b text-sigma-tx2',
        };

        $now       = now();
        $createdAt = isset($row->created_at) ? \Illuminate\Support\Carbon::parse($row->created_at) : $now;
        $closedAt  = isset($row->closed_at)  ? \Illuminate\Support\Carbon::parse($row->closed_at)  : null;

        return new self(
            id:          (int) ($row->id ?? 0),
            date:        $createdAt->format('Y-m-d'),
            user:        (string) ($row->user_name ?? 'Unknown'),
            facility:    isset($row->facility) ? (string) $row->facility : null,
            asset:       isset($row->asset_full) ? (string) $row->asset_full : null,
            priority:    isset($row->priority) ? (string) $row->priority : null,
            status:      "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>",
            description: isset($row->description) ? (string) $row->description : null,
            assignee:    isset($row->assignee_name) ? (string) $row->assignee_name : null,
            days:        (int) $createdAt->diffInDays($closedAt ?? $now),
            started_at:  isset($row->started_at) ? (string) $row->started_at : null,
            attended:    isset($row->attended_at) ? (string) $row->attended_at : null,
            closed:      isset($row->closed_at)  ? (string) $row->closed_at  : null,
            time:        (float) ($row->time_sum ?? 0),
            sgc:         isset($row->sgc) ? (string) $row->sgc : null,
            rating:      isset($row->rating) ? (int) $row->rating : null,
        );
    }
}
