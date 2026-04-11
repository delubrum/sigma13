<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface to provide sidebar menu items without coupling to a specific module.
 */
interface SidebarProviderContract
{
    /**
     * @return array<int, array{title: string, icon: string, url: ?string, children: array<int, array{title: string, url: string}>}>
     */
    public function getMenuItemsForUser(Authenticatable $user): array;
}
