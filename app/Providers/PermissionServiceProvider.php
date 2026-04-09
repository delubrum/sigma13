<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function (User $user, $ability): ?true {
            $permissions = $user->permissions;

            if (! is_array($permissions) || $permissions === []) {
                return null;
            }

            $permissionIds = array_map(strval(...), $permissions);

            if (in_array((string) $ability, $permissionIds, true)) {
                return true;
            }

            return null;
        });

        Blade::if('canany', function ($permissions): bool {
            if (! Auth::check()) {
                return false;
            }

            $user = Auth::user();
            if (! $user instanceof User) {
                return false;
            }
            $userPermissions = $user->permissions;

            if (! is_array($userPermissions) || $userPermissions === []) {
                return false;
            }

            $permissionIds = array_map(strval(...), $userPermissions);

            return array_any((array) $permissions, fn ($permission): bool => in_array((string) $permission, $permissionIds, true));
        });
    }
}
