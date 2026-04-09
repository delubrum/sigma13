<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use Spatie\LaravelData\Data;

final class Assignment extends Data
{
    public function __construct(
        public readonly int $employee_id,
        /** @var list<string> */
        public readonly array $hardware,
        /** @var list<string> */
        public readonly array $software,
        public readonly ?string $notes,
    ) {}
}
