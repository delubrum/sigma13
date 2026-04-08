<?php

declare(strict_types=1);

namespace App\Data\Recruitment;

use App\Models\Recruitment;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly string $user,
        public readonly string $approver,
        public readonly string $assignee,
        public readonly string $profile,
        public readonly string $division,
        public readonly string $area,
        public readonly string $qty,
        public readonly float $conversion,
        public readonly int $days,
        public readonly string $status,
    ) {}

    public static function fromModel(Recruitment $r): self
    {
        $hiredCountAttr = $r->getAttribute('hired_count');
        $hiredCount = is_scalar($hiredCountAttr) ? (int) $hiredCountAttr : 0;
        $qtyParam   = is_scalar($r->qty) ? (int) $r->qty : 1;
        $qtySafe    = max($qtyParam, 1);
        
        $conversion = round(($hiredCount / $qtySafe) * 100, 2);

        return new self(
            id: $r->id,
            date: $r->created_at ? $r->created_at->format('Y-m-d') : '—',
            user: is_scalar($u = $r->getAttribute('user_name')) ? (string) $u : '—',
            approver: is_scalar($a = $r->getAttribute('approver_name') ?? $r->approver) ? (string) $a : '—',
            assignee: is_scalar($as = $r->getAttribute('assignee_name')) ? (string) $as : '—',
            profile: is_scalar($p = $r->getAttribute('profile_name')) ? (string) $p : '—',
            division: is_scalar($d = $r->getAttribute('division_name')) ? (string) $d : '—',
            area: is_scalar($ar = $r->getAttribute('area_name')) ? (string) $ar : '—',
            qty: "{$hiredCount} / {$qtyParam}",
            conversion: (float) $conversion,
            days: is_scalar($days = $r->getAttribute('days_remaining')) ? (int) $days : 0,
            status: is_scalar($s = $r->status) ? (string) $s : 'Approval',
        );
    }
}
