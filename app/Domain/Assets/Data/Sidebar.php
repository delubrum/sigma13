<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $serial,
        public readonly string $sap,
        public readonly ?string $hostname,
        #[MapInputName('work_mode')]
        public readonly ?string $workMode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly string $status,
        #[MapInputName('currentAssignment.employee.name')]
        public readonly ?string $assignee,
        #[MapInputName('currentAssignment.created_at')]
        public readonly ?string $assignedAt,
        #[MapInputName('url')]
        public readonly ?string $photoUrl,
        public readonly ?string $qrUrl,
    ) {}

}
