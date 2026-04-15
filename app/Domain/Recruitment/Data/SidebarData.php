<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Recruitment\Models\Recruitment;
use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly string $date,
        public readonly string $creator,
        public readonly string $approver,
        public readonly ?string $assignee,
        public readonly ?string $profile,
        public readonly ?string $division,
        public readonly ?string $area,
        public readonly int $qty,
        public readonly int $hired_count,
        public readonly int $conversion_pct,
        public readonly ?string $city,
        public readonly ?string $contract,
        public readonly ?string $srange,
        public readonly ?string $start_date,
        public readonly ?string $cause,
        public readonly ?string $rejection,
        public readonly ?string $approved_at,
        public readonly ?string $closed_at,
    ) {}

    public static function fromModel(Recruitment $r, string $creatorName, string $approverName, ?string $assigneeName, ?string $profileName, ?string $divisionName, ?string $areaName, int $hiredCount): self
    {
        $qty = $r->qty > 0 ? $r->qty : 1;

        return new self(
            id: $r->id,
            status: $r->status,
            date: $r->created_at?->format('Y-m-d') ?? '',
            creator: $creatorName,
            approver: $approverName,
            assignee: $assigneeName,
            profile: $profileName,
            division: $divisionName,
            area: $areaName,
            qty: $r->qty,
            hired_count: $hiredCount,
            conversion_pct: (int) round($hiredCount / $qty * 100),
            city: $r->city,
            contract: $r->contract,
            srange: $r->srange,
            start_date: $r->start_date,
            cause: $r->cause,
            rejection: $r->rejection,
            approved_at: $r->approved_at?->format('Y-m-d H:i'),
            closed_at: $r->closed_at?->format('Y-m-d H:i'),
        );
    }
}
