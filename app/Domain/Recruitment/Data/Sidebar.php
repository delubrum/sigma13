<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        #[MapInputName('requestor.name')]
        public readonly ?string $user,
        public readonly ?string $approver,
        #[MapInputName('assignee.name')]
        public readonly ?string $assignee,
        public readonly ?string $city,
        public readonly ?int $qty,
        #[MapInputName('cause')]
        public readonly ?string $reason,
        public readonly ?string $complexity,
        #[MapInputName('created_at')]
        public readonly ?string $createdAt,
        #[MapInputName('approved_at')]
        public readonly ?string $approvedAt,
        #[MapInputName('closed_at')]
        public readonly ?string $closedAt,
    ) {}

}
