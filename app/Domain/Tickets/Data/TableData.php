<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Column;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Tipo', width: 120, headerFilter: 'input')]
        public string $type,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public string $date,

        #[Column(title: 'Usuario', width: 150, headerFilter: 'input')]
        public string $user,

        #[Column(title: 'Sede', width: 150, headerFilter: 'input')]
        public ?string $facility,

        #[Column(title: 'Prioridad', width: 100, headerFilter: 'input')]
        public ?string $priority,

        #[Column(title: 'Descripción', width: 250, formatter: 'textarea', headerFilter: 'input')]
        public ?string $description,

        #[Column(title: 'Días', width: 80, hozAlign: 'center', headerFilter: 'number')]
        public int $days,

        #[Column(title: 'Iniciado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $started,

        #[Column(title: 'Cerrado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $closed,

        #[Column(
            title: 'Estado',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => ['Open' => 'Open', 'Started' => 'Started', 'Closed' => 'Closed', 'Rejected' => 'Rejected'],
                'clearable' => true,
            ]
        )]
        public string $status,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $status = (string) ($row->status ?? 'Open');
        $color = match ($status) {
            'Open' => 'border-blue-500 text-blue-500',
            'Started' => 'border-yellow-500 text-yellow-500',
            'Closed' => 'border-green-500 text-green-500',
            'Rejected' => 'border-red-500 text-red-500',
            default => 'border-sigma-b text-sigma-tx2',
        };

        $now = now();
        $createdAt = isset($row->created_at) ? Date::parse((string) $row->created_at) : $now;
        $closedAt = isset($row->closed_at) ? Date::parse((string) $row->closed_at) : null;

        return new self(
            id: (int) ($row->id ?? 0),
            type: (string) ($row->kind ?? ''),
            date: $createdAt->format('Y-m-d'),
            user: (string) ($row->user_name ?? 'Unknown'),
            facility: isset($row->facility) ? (string) $row->facility : null,
            priority: isset($row->priority) ? (string) $row->priority : null,
            description: isset($row->description) ? (string) $row->description : null,
            days: (int) $createdAt->diffInDays($closedAt ?? $now),
            started: isset($row->started_at) ? (string) $row->started_at : null,
            closed: isset($row->closed_at) ? (string) $row->closed_at : null,
            status: "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>",
        );
    }
}
