<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\Shared\Config;

interface HasModule
{
    public function config(): Config;
}
