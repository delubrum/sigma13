<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\DomainServiceProvider;
use App\Providers\PermissionServiceProvider;

return [
    AppServiceProvider::class,
    PermissionServiceProvider::class,
    DomainServiceProvider::class,
];
