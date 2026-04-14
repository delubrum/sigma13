<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Assets\Models\Asset;
use Spatie\LaravelData\Data;

final class PublicShowResourceData extends Data
{
    public function __construct(
        public int $id,
        public ?string $serial,
        public ?string $brand,
        public ?string $model,
        public ?string $status,
        public ?string $hostname,
        public ?string $sap,
        public ?string $location,
        public ?string $work_mode,
        public ?string $phone,
        public ?string $cpu,
        public ?string $ram,
        public ?string $ssd,
        public ?string $hdd,
        public ?string $so,
        public ?string $profile_photo_url,
        public ?string $assignee,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        return new self(
            id: $asset->id,
            serial: $asset->serial,
            brand: $asset->brand,
            model: $asset->model,
            status: $asset->status,
            hostname: $asset->hostname,
            sap: $asset->sap,
            location: $asset->location,
            work_mode: $asset->work_mode,
            phone: $asset->phone,
            cpu: $asset->cpu,
            ram: $asset->ram,
            ssd: $asset->ssd,
            hdd: $asset->hdd,
            so: $asset->so,
            profile_photo_url: $asset->profilePhotoUrl,
            assignee: $asset->currentAssignment?->employee?->name,
        );
    }
}
