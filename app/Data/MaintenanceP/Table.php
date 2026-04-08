<?php

declare(strict_types=1);

namespace App\Data\MaintenanceP;

use App\Models\MntPreventive;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $start,
        public readonly ?string $end,
        public readonly ?string $asset,
        public readonly string $status,
        public readonly string $days,
        public readonly ?string $activity,
        public readonly ?string $frequency,
        public readonly ?string $started,
        public readonly ?string $attended,
        public readonly ?string $closed,
    ) {}

    public static function fromModel(MntPreventive $record): self
    {
        $daysDiff = (int) ($record->getAttribute('days_diff_count') ?? 0);
        $color = $daysDiff >= 0 ? 'text-emerald-500' : 'text-red-500';
        if (! empty($record->closed_at)) {
            $color = 'text-slate-400';
        }

        return new self(
            id: $record->id,
            start: $record->scheduled_start,
            end: $record->scheduled_end,
            asset: mb_convert_case((string) ($record->getAttribute('asset_hostname') ?? '—'), MB_CASE_TITLE, 'UTF-8'),
            status: $record->status ?? 'Open',
            days: sprintf("<span class='font-bold %s'>%d</span>", $color, $daysDiff),
            activity: (string) ($record->getAttribute('activity') ?? '—'),
            frequency: (string) ($record->getAttribute('frequency_value') ?? '—'),
            started: $record->started,
            attended: $record->attended,
            closed: $record->closed_at?->format('Y-m-d H:i'),
        );
    }
}
