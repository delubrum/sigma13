<?php

declare(strict_types=1);

namespace App\Data\Recruitment;

use Spatie\LaravelData\Data;

final class CreateRecruitment extends Data
{
    public function __construct(
        public readonly int $profile_id,
        public readonly string $approver,
        public readonly ?string $city,
        public readonly int $qty,
        public readonly ?string $contract,
        public readonly ?string $cause,
        public readonly ?string $srange,
        public readonly ?string $replaces,
        public readonly ?string $start_date,
        public readonly ?string $others,
    ) {}
}
