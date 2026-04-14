<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Data;

use Spatie\LaravelData\Data;

final class CauseUpsertData extends Data
{
    public function __construct(
        public readonly ?int    $id,
        public readonly int     $improvement_id,
        public readonly string  $reason,
        public readonly int     $method,
        public readonly ?string $probable,
        /** @var array<int,string>|null */
        public readonly ?array  $whys,
        public readonly ?string $file,
    ) {}
}
