<?php

declare(strict_types=1);

namespace App\Contracts;

interface HasOptions
{
    /**
     * @param  array<string, mixed>  $params
     * @return list<array{value: mixed, label: string}>|array<int|string, string>
     */
    public function resolveOptions(string $key, array $params): array;
}
