<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Assets\Models\Asset;
use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $serial,
        public readonly ?string $sap,
        public readonly ?string $hostname,
        public readonly ?string $workMode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly ?string $assignee,
        public readonly ?string $assignedAt,
        public readonly ?string $photoUrl,
        public readonly ?string $qrUrl,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        return new self(
            id: $asset->id,
            serial: $asset->serial,
            sap: $asset->sap,
            hostname: $asset->hostname,
            workMode: $asset->work_mode,
            location: $asset->location,
            phone: $asset->phone,
            status: $asset->status ?? 'available',
            assignee: $asset->currentAssignment?->employee?->name,
            assignedAt: $asset->currentAssignment?->created_at?->format('d/m/Y'),
            photoUrl: $asset->profilePhotoUrl,
            qrUrl: $asset->qrUrl,
        );
    }
}
