<?php

declare(strict_types=1);

namespace App\Contracts;

use Spatie\LaravelData\Data;

interface HasDetail
{
    public function sidebarData(int $id): Data;
}
