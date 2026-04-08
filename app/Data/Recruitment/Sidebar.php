<?php

declare(strict_types=1);

namespace App\Data\Recruitment;

use App\Models\Recruitment;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly ?string $user,
        public readonly ?string $approver,
        public readonly ?string $assignee,
        public readonly ?string $city,
        public readonly ?int $qty,
        public readonly ?string $reason,
        public readonly ?string $complexity,
        public readonly ?string $createdAt,
        public readonly ?string $approvedAt,
        public readonly ?string $closedAt,
    ) {}

    public static function fromModel(Recruitment $recruitment): self
    {
        return new self(
            id: $recruitment->id,
            status: $recruitment->status ?? 'Approval',
            user: is_scalar($u = $recruitment->requestor?->getAttribute('name')) ? (string) $u : '—',
            approver: is_scalar($recruitment->approver) ? (string) $recruitment->approver : '—',
            assignee: is_scalar($a = $recruitment->assignee?->getAttribute('name')) ? (string) $a : '—',
            city: $recruitment->city,
            qty: $recruitment->qty,
            reason: $recruitment->cause,
            complexity: (string)$recruitment->complexity,
            createdAt: $recruitment->created_at ? $recruitment->created_at->format('Y-m-d H:i') : null,
            approvedAt: $recruitment->approved_at ? $recruitment->approved_at->format('Y-m-d H:i') : null,
            closedAt: $recruitment->closed_at ? $recruitment->closed_at->format('Y-m-d H:i') : null,
        );
    }
}
