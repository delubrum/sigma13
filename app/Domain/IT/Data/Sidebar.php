<?php

declare(strict_types=1);

namespace App\Domain\IT\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly ?string $priority,
        public readonly ?string $facility,
        public readonly ?string $description,
        #[MapInputName('assignee.name')]
        public readonly ?string $assignee,
        #[MapInputName('requestor.name')]
        public readonly ?string $user,
        public readonly ?string $sgc,
        public readonly ?int $rating,
        #[MapInputName('created_at')]
        public readonly ?string $createdAt,
        #[MapInputName('started_at')]
        public readonly ?string $startedAt,
        #[MapInputName('closed_at')]
        public readonly ?string $closedAt,
    ) {}

}
