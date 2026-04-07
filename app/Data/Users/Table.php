<?php

declare(strict_types=1);

namespace App\Data\Users;

use App\Models\User;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly string $isActive,
        public readonly string $createdAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            document: $user->document ?? '—',
            isActive: $user->is_active
                ? '<span class="px-2 py-0.5 rounded border border-sigma-b bg-sigma-bg2 text-sigma-tx font-bold uppercase text-[10px]">Activo</span>'
                : '<span class="px-2 py-0.5 rounded border border-sigma-b text-sigma-tx2 opacity-50 font-bold uppercase text-[10px]">Inactivo</span>',
            createdAt: $user->created_at?->format('d/m/Y H:i') ?? '—',
        );
    }
}
