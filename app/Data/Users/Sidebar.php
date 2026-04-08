<?php

declare(strict_types=1);

namespace App\Data\Users;

use App\Models\User;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $document,
        public readonly bool $isActive,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: (int) $user->id,
            name: $user->name,
            email: $user->email,
            document: $user->document,
            isActive: (bool) $user->is_active,
        );
    }
}
