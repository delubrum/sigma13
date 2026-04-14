<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'left', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Date', field: 'created_at', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public string $created_at,

        #[Column(title: 'User', width: 150, hozAlign: 'left', headerFilter: 'input')]
        public string $user,

        #[Column(title: 'Facility', width: 120, hozAlign: 'left', headerFilter: 'input')]
        public ?string $facility,

        #[Column(title: 'Asset', width: 200, hozAlign: 'left', headerFilter: 'input')]
        public ?string $asset,

        #[Column(title: 'Priority', width: 100, hozAlign: 'left', headerFilter: 'input')]
        public ?string $priority,

        #[Column(
            title: 'Status',
            width: 110,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values'    => ['Open' => 'Open', 'Started' => 'Started', 'Attended' => 'Attended', 'Closed' => 'Closed', 'Rated' => 'Rated', 'Rejected' => 'Rejected'],
                'clearable' => true,
            ]
        )]
        public string $status,

        #[Column(title: 'Description', width: 250, formatter: 'html', hozAlign: 'left', headerFilter: 'input')]
        public ?string $description,

        #[Column(title: 'Assignee', width: 150, hozAlign: 'left', headerFilter: 'input')]
        public ?string $assignee,

        #[Column(title: 'Days', width: 100, hozAlign: 'left', headerFilter: 'input')]
        public int $days,

        #[Column(title: 'Started', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $started,

        #[Column(title: 'Attended', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $attended,

        #[Column(title: 'Closed', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $closed,

        #[Column(title: 'Hours Worked', field: 'time', width: 100, hozAlign: 'left', headerFilter: 'input')]
        public float $time,

        #[Column(title: 'SGC', width: 120, hozAlign: 'left', headerFilter: 'input')]
        public ?string $sgc,

        #[Column(title: 'Cause', width: 120, hozAlign: 'left', headerFilter: 'input')]
        public ?string $cause,

        #[Column(title: 'Rating', width: 100, hozAlign: 'left', headerFilter: 'number')]
        public ?int $rating,
    ) {}

    public static function fromModel(object $row): self
    {
        $now       = now();
        $createdAt = isset($row->created_at) ? \Illuminate\Support\Carbon::parse($row->created_at) : $now;
        $closedAt  = isset($row->closed_at)  ? \Illuminate\Support\Carbon::parse($row->closed_at)  : null;

        return new self(
            id:          (int) ($row->id ?? 0),
            created_at:  $createdAt->format('Y-m-d'),
            user:        (string) ($row->user_name ?? ''),
            facility:    isset($row->facility)       ? (string) $row->facility       : null,
            asset:       isset($row->asset_full)     ? (string) $row->asset_full     : null,
            priority:    isset($row->priority)       ? (string) $row->priority       : null,
            status:      (string) ($row->status ?? 'Open'),
            description: isset($row->description)   ? (string) $row->description    : null,
            assignee:    isset($row->assignee_name)  ? (string) $row->assignee_name  : null,
            days:        (int) $createdAt->diffInDays($closedAt ?? $now),
            started:     isset($row->started_at)    ? (string) $row->started_at     : null,
            attended:    isset($row->ended_at)       ? (string) $row->ended_at       : null,
            closed:      isset($row->closed_at)     ? (string) $row->closed_at      : null,
            time:        (float) ($row->time_sum ?? 0),
            sgc:         isset($row->sgc)           ? (string) $row->sgc            : null,
            cause:       isset($row->cause)         ? (string) $row->cause          : null,
            rating:      isset($row->rating)        ? (int) $row->rating            : null,
        );
    }
}
