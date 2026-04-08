<?php

declare(strict_types=1);

namespace App\Data\Assets\Modals;

use Spatie\LaravelData\Data;

final class ReturnAsset extends Data
{
    public function __construct(
        public ?string $notes = null,
    ) {}
}
