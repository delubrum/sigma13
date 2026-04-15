<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Column;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Fecha', width: 110, hozAlign: 'center', headerFilter: 'input')]
        public string $date,

        #[Column(title: 'Creador', width: 140, headerFilter: 'input')]
        public string $creator,

        #[Column(title: 'Aprobador', width: 140, headerFilter: 'input')]
        public string $approver,

        #[Column(title: 'Asignado', width: 140, headerFilter: 'input')]
        public ?string $assignee,

        #[Column(title: 'Perfil', width: 160, headerFilter: 'input')]
        public ?string $profile,

        #[Column(title: 'División', width: 130, headerFilter: 'input')]
        public ?string $division,

        #[Column(title: 'Área', width: 130, headerFilter: 'input')]
        public ?string $area,

        #[Column(title: 'Qty', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public string $qty,

        #[Column(
            title: 'Conversión',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'input'
        )]
        public string $conversion,

        #[Column(title: 'Días', width: 70, hozAlign: 'center', headerFilter: 'number')]
        public int $days,

        #[Column(
            title: 'Estado',
            width: 110,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'approval' => 'Approval',
                    'approved' => 'Approved',
                    'closed' => 'Closed',
                    'rejected' => 'Rejected',
                    'review' => 'Review',
                ],
                'clearable' => true,
            ]
        )]
        public string $status,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $status = (string) ($row->status ?? 'approval');
        $color = match ($status) {
            'approved' => 'border-green-500 text-green-500',
            'approval' => 'border-yellow-500 text-yellow-500',
            'review' => 'border-blue-500 text-blue-500',
            'closed' => 'border-sigma-b text-sigma-tx2',
            'rejected' => 'border-red-500 text-red-500',
            default => 'border-sigma-b text-sigma-tx2',
        };

        $pct = (int) ($row->conversion_pct ?? 0);
        $convHtml = "<div class=\"relative h-3.5 bg-gray-200 rounded\" style=\"min-width:60px\">
            <div class=\"absolute inset-0 bg-sigma-primary rounded\" style=\"width:{$pct}%\"></div>
            <span class=\"absolute inset-0 flex items-center justify-center text-[10px] font-bold\">{$pct}%</span>
        </div>";

        $createdAt = isset($row->created_at)
            ? Date::parse((string) $row->created_at)
            : now();

        return new self(
            id: (int) ($row->id ?? 0),
            date: $createdAt->format('Y-m-d'),
            creator: (string) ($row->creator_name ?? ''),
            approver: (string) ($row->approver_name ?? ($row->approver ?? '')),
            assignee: isset($row->assignee_name) ? (string) $row->assignee_name : null,
            profile: isset($row->profile_name) ? (string) $row->profile_name : null,
            division: isset($row->division_name) ? (string) $row->division_name : null,
            area: isset($row->area_name) ? (string) $row->area_name : null,
            qty: (string) ($row->hired_qty ?? ($row->qty ?? '0')),
            conversion: $convHtml,
            days: (int) ($row->days_open ?? 0),
            status: "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>",
        );
    }
}
