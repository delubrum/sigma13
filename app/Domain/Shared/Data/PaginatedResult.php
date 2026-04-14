<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

/**
 * @template T of Data
 */
final readonly class PaginatedResult
{
    /**
     * @param  list<T>  $items
     */
    public function __construct(
        public array $items,
        public int $lastPage,
        public int $total,
    ) {}
}
