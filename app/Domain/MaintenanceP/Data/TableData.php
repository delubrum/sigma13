<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', field: 'id', width: 60, hozAlign: 'center')]
        public int $id,

        #[Column(title: 'Start', field: 'start', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public string $start,

        #[Column(title: 'End', field: 'end', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public string $end,

        #[Column(title: 'Asset', field: 'asset', width: 200, headerFilter: 'input')]
        public string $asset,

        #[Column(title: 'Activity', field: 'activity', width: 200, headerFilter: 'input')]
        public string $activity,

        #[Column(title: 'Frequency', field: 'frequency', width: 120, headerFilter: 'input')]
        public string $frequency,

        #[Column(
            title: 'Status',
            field: 'status',
            width: 110,
            hozAlign: 'center',
            headerFilter: 'list',
            headerFilterParams: [
                'values'    => ['Open' => 'Open', 'Started' => 'Started', 'Attended' => 'Attended', 'Closed' => 'Closed'],
                'clearable' => true,
            ]
        )]
        public string $status,

        #[Column(title: 'Days', field: 'days', width: 80, hozAlign: 'center', formatter: 'html')]
        public string $days,

        #[Column(title: 'Started', field: 'started', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $started,

        #[Column(title: 'Attended', field: 'attended', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $attended,

        #[Column(title: 'Closed', field: 'closed', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $closed,
    ) {}

    public static function fromModel(object $row): self
    {
        $daysDiff = (int) ($row->days_diff ?? 0);
        $isClosed = ! empty($row->closed_at) && $row->closed_at !== '0000-00-00 00:00:00';
        $color    = $isClosed ? 'text-gray-500' : ($daysDiff >= 0 ? 'text-green-500' : 'text-red-500');

        return new self(
            id:        (int) ($row->id ?? 0),
            start:     (string) ($row->scheduled_start ?? ''),
            end:       (string) ($row->scheduled_end ?? ''),
            asset:     (string) ($row->asset_full ?? ''),
            activity:  (string) ($row->activity_name ?? ''),
            frequency: (string) ($row->frequency ?? ''),
            status:    (string) ($row->status ?? 'Open'),
            days:      "<span class=\"font-bold {$color}\">{$daysDiff}</span>",
            started:   isset($row->started)   ? (string) $row->started   : null,
            attended:  isset($row->attended)  ? (string) $row->attended  : null,
            closed:    isset($row->closed_at) ? (string) $row->closed_at : null,
        );
    }
}
