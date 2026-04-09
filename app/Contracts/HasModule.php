<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Domain\Shared\Data\Config;

interface HasModule
{
    public function config(): Config;
}
