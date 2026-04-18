<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use Spatie\LaravelData\Data;

final class PermissionsTabData extends Data
{
    /**
     * @param  list<int>                                                    $userPermissions
     * @param  array<string, list<array{id:int,name:string,title:string|null}>>  $permissions
     */
    public function __construct(
        public readonly int   $id,
        public readonly array $userPermissions,
        public readonly array $permissions,
    ) {}
}
