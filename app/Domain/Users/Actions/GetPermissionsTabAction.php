<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Data\PermissionsTabData;
use App\Domain\Users\Models\Permission;
use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPermissionsTabAction
{
    use AsAction;

    public function handle(int $id): PermissionsTabData
    {
        $user = User::findOrFail($id);

        $raw             = $user->permissions ?? [];
        $userPermissions = array_map(intval(...), is_array($raw) ? $raw : (array) json_decode((string) $raw, true));

        $permissions = Permission::where('kind', 'action')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('category')
            ->map(fn ($items) => $items->map(fn ($p): array => [
                'id'    => $p->id,
                'name'  => $p->name,
                'title' => $p->title,
            ])->values()->all())
            ->all();

        return new PermissionsTabData(
            id:              $user->id,
            userPermissions: $userPermissions,
            permissions:     $permissions,
        );
    }
}
