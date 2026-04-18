<?php

declare(strict_types=1);

namespace App\Contracts;

interface HasPatch
{
    /**
     * @return array{table: string, fields: array<string, list<string>>}
     * fields: field => div ids to refresh on change (empty = no extra refresh)
     */
    public function patchConfig(int $id): array;
}
