<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\Shared\Data\SidebarItem;
use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    /**
     * @param  list<SidebarItem>               $properties
     * @param  list<array{id:int,label:string}> $assets
     * @param  list<array{id:int,name:string}>  $technicians
     */
    public function __construct(
        public readonly int    $id,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly string $color,
        public readonly array  $properties,
        public readonly string $description   = '',
        public readonly bool   $canEdit       = false,
        public readonly bool   $canClose      = false,
        public readonly ?int   $assetId       = null,
        public readonly string $assetLabel    = '',
        public readonly ?int   $assigneeId    = null,
        public readonly string $assigneeName  = '',
        public readonly string $priority      = '',
        public readonly string $sgcCode       = '',
        public readonly string $rootCause     = '',
        public readonly array  $assets        = [],
        public readonly array  $technicians   = [],
    ) {}
}
