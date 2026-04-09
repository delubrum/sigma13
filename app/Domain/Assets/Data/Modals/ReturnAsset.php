<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use Spatie\LaravelData\Data;

final class ReturnAsset extends Data
{
    public function __construct(
        public ?string $notes = null,
    ) {}
}
