<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Data;

use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $name,
        public readonly string $division,
        public readonly string $area,
        public readonly string $reportsTo,
        public readonly string $reportsList,
        public readonly string $workMode,
        public readonly string $rank,
        public readonly string $schedule,
        public readonly string $travel,
        public readonly string $relocation,
        public readonly string $lang,
        public readonly string $experience,
        public readonly string $obs,
        public readonly string $mission,
        public readonly string $createdAt,
        public readonly bool $canEdit,
    ) {}
}
