<?php

declare(strict_types=1);

namespace App\Domain\HR\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'left', headerFilter: 'input')]
        public string $id,

        #[Column(title: 'Name', headerHozAlign: 'left', headerFilter: 'input')]
        public string $name,

        #[Column(title: 'Division', headerHozAlign: 'left', headerFilter: 'input')]
        public string $division,

        #[Column(title: 'Profile', headerHozAlign: 'left', headerFilter: 'input')]
        public string $profile,

        #[Column(title: 'City', headerHozAlign: 'left', headerFilter: 'input')]
        public string $city,

        #[Column(title: 'Start Date', field: 'start_date', headerHozAlign: 'left', headerFilter: 'input')]
        public string $start_date,

        #[Column(title: 'Status', hozAlign: 'center', formatter: 'html', headerFilter: 'input')]
        public string $status,

        #[Column(title: 'Last Update', field: 'updated_at', headerHozAlign: 'left', headerFilter: 'input')]
        public string $updated_at,
    ) {}

    public static function fromModel(object $row): self
    {
        $active = (bool) ($row->status ?? false);
        $color  = $active ? 'border-green-500 text-green-500' : 'border-red-500 text-red-500';
        $label  = $active ? 'Activo' : 'Inactivo';

        return new self(
            id:         (string) ($row->id ?? ''),
            name:       (string) ($row->name ?? ''),
            division:   (string) ($row->division_name ?? 'N/A'),
            profile:    (string) ($row->profile_name ?? 'N/A'),
            city:       (string) ($row->city ?? ''),
            start_date: isset($row->start_date) ? date('Y-m-d', strtotime((string) $row->start_date)) : '',
            status:     "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$label}</span>",
            updated_at: isset($row->latest_update) ? date('Y-m-d H:i', strtotime((string) $row->latest_update)) : 'N/A',
        );
    }
}
