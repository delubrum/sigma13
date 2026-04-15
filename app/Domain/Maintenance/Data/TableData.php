<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Maintenance\Models\Maintenance;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center')]
        public readonly int $id,

        #[Column(title: 'Date', width: 150)]
        public readonly ?string $created_at,

        #[Column(title: 'User', width: 120)]
        public readonly string $user,

        #[Column(title: 'Facility', width: 100)]
        public readonly string $facility,

        #[Column(title: 'Asset', width: 200)]
        public readonly string $asset,

        #[Column(title: 'Priority', width: 100, hozAlign: 'center', formatter: 'html')]
        public readonly string $priority,

        #[Column(
            title: 'Status',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'Open' => 'Open',
                    'Started' => 'Started',
                    'Attended' => 'Attended',
                    'Closed' => 'Closed',
                    'Rated' => 'Rated',
                    'Rejected' => 'Rejected',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Description', width: 250, formatter: 'html')]
        public readonly ?string $description,

        #[Column(title: 'Assignee', width: 120)]
        public readonly string $assignee,

        #[Column(title: 'Days', width: 80, hozAlign: 'center')]
        public readonly int $days,

        #[Column(title: 'Started At', width: 120, hozAlign: 'center')]
        public readonly ?string $started,

        #[Column(title: 'Hours', width: 80, hozAlign: 'center')]
        public readonly float|int $time,

        #[Column(title: 'SGC', width: 100)]
        public readonly ?string $sgc,

        #[Column(title: 'Cause', width: 120)]
        public readonly ?string $cause,

        #[Column(title: 'Rating', width: 80, hozAlign: 'center')]
        public readonly ?int $rating,
    ) {}

    public static function fromModel(mixed $mnt): self
    {
        /** @var Maintenance $mnt */
        $dateCreated = $mnt->created_at;
        $dateClosed = $mnt->closed_at ?? now();
        $days = $dateCreated ? (int) $dateCreated->diffInDays($dateClosed) : 0;

        return new self(
            id: (int) $mnt->id,
            created_at: $mnt->created_at?->format('Y-m-d H:i') ?? '',
            user: $mnt->user->username ?? '—',
            facility: $mnt->facility ?? '—',
            asset: self::formatAsset($mnt),
            priority: self::formatPriority($mnt->priority),
            status: self::formatStatus($mnt->status ?? 'Open'),
            description: $mnt->description,
            assignee: $mnt->assignee->username ?? '—',
            days: $days,
            started: $mnt->started_at?->format('Y-m-d') ?? '',
            time: (float) ($mnt->time_sum ?? 0),
            sgc: $mnt->sgc,
            cause: $mnt->root_cause,
            rating: $mnt->rating,
        );
    }

    private static function formatAsset(Maintenance $mnt): string
    {
        if (! $mnt->asset) {
            return '—';
        }
        $asset = $mnt->asset;
        $parts = array_filter([$asset->hostname, $asset->serial, $asset->sap]);
        return mb_convert_case(implode(' | ', $parts), MB_CASE_TITLE, 'UTF-8');
    }

    private static function formatPriority(?string $priority): string
    {
        if (! $priority) return '—';
        $color = match (strtolower($priority)) {
            'high' => 'border-red-500 text-red-500',
            'medium' => 'border-orange-500 text-orange-500',
            default => 'border-gray-500 text-gray-500',
        };
        return "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$priority}</span>";
    }

    private static function formatStatus(string $status): string
    {
        $color = match (strtolower($status)) {
            'open' => 'border-gray-500 text-gray-500',
            'started' => 'border-yellow-500 text-yellow-500',
            'attended' => 'border-purple-500 text-purple-500',
            'closed' => 'border-blue-500 text-blue-500',
            'rated' => 'border-green-500 text-green-500',
            'rejected' => 'border-red-500 text-red-500',
            default => 'border-sigma-b text-sigma-tx2',
        };
        return "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>";
    }
}
