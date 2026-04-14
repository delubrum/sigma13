<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Date', headerHozAlign: 'center', headerFilter: 'input')]
        public string $date,

        #[Column(title: 'Creator', field: 'user', headerHozAlign: 'center', headerFilter: 'input')]
        public string $user,

        #[Column(title: 'Approver', headerHozAlign: 'center', headerFilter: 'input')]
        public ?string $approver,

        #[Column(title: 'Assignee', headerHozAlign: 'center', headerFilter: 'input')]
        public ?string $assignee,

        #[Column(title: 'Profile', headerHozAlign: 'center', headerFilter: 'input')]
        public string $profile,

        #[Column(title: 'Division', headerHozAlign: 'center', headerFilter: 'input')]
        public string $division,

        #[Column(title: 'Area', headerHozAlign: 'center', headerFilter: 'input')]
        public string $area,

        #[Column(title: 'Quantity', field: 'qty', headerFilter: 'input')]
        public int $qty,

        #[Column(title: 'Conversion', formatter: 'progress', headerHozAlign: 'center', headerFilter: 'input')]
        public int $conversion,

        #[Column(title: 'Days', headerHozAlign: 'center', headerFilter: 'input')]
        public int $days,

        #[Column(
            title: 'Status',
            headerHozAlign: 'center',
            hozAlign: 'center',
            headerFilter: 'list',
            headerFilterParams: [
                'values'    => ['approved' => 'Approved', 'approval' => 'Approval', 'closed' => 'Closed'],
                'clearable' => true,
            ]
        )]
        public string $status,
    ) {}

    public static function fromModel(object $row): self
    {
        return new self(
            id:         (int) ($row->id ?? 0),
            date:       isset($row->created_at) ? date('Y-m-d', strtotime((string) $row->created_at)) : '',
            user:       (string) ($row->creator_name  ?? ''),
            approver:   isset($row->approver)      ? (string) $row->approver      : null,
            assignee:   isset($row->assignee_name) ? (string) $row->assignee_name : null,
            profile:    (string) ($row->profile_name  ?? ''),
            division:   (string) ($row->division_name ?? ''),
            area:       (string) ($row->area_name     ?? ''),
            qty:        (int) ($row->qty ?? 0),
            conversion: (int) ($row->conversion ?? 0),
            days:       (int) ($row->days_passed ?? 0),
            status:     (string) ($row->status ?? ''),
        );
    }
}
