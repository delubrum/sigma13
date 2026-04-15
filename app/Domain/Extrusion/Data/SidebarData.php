<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Data;

use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $geometry_shape,
        public readonly string $company_id,
        public readonly string $category_id,
        public readonly string $b,
        public readonly string $h,
        public readonly string $e1,
        public readonly string $e2,
        /** @var list<string> */
        public readonly array  $clicks,
        /** @var list<string> */
        public readonly array  $systema,
        /** @var list<array{name:string,url:string}> */
        public readonly array  $files,
        /** @var list<string> */
        public readonly array  $allShapes,
        /** @var list<string> */
        public readonly array  $allCategories,
        /** @var list<string> */
        public readonly array  $allSystems,
        /** @var list<string> */
        public readonly array  $allCompanies,
    ) {}
}
