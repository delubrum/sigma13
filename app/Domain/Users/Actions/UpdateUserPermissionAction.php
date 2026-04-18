<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateUserPermissionAction
{
    use AsAction;

    public function handle(int $userId, int $permissionId): void
    {
        $user    = User::findOrFail($userId);
        $current = array_map(intval(...), is_array($user->permissions) ? $user->permissions : []);

        if (in_array($permissionId, $current, true)) {
            $updated = array_values(array_filter($current, fn (int $p): bool => $p !== $permissionId));
        } else {
            $updated = [...$current, $permissionId];
        }

        $user->update(['permissions' => $updated]);
    }
}
