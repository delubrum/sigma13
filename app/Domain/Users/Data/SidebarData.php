<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $document,
        #[MapInputName('is_active')]
        public readonly bool $isActive,
        /** @var Field[] */
        public readonly array $fields = [],
    ) {}

}
