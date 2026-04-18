<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Adapters;

use App\Domain\Shared\Web\Adapters\AbstractTabAdapter;
use App\Domain\Users\Actions\GetPermissionsTabAction;
use App\Domain\Users\Actions\UpdateUserPermissionAction;
use App\Domain\Users\Actions\UpdateUserStatusAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PermissionsAdapter extends AbstractTabAdapter
{
    protected function view(): string { return 'users::tabs.detail'; }

    protected function getData(int $id): mixed { return GetPermissionsTabAction::run($id); }

    public function asUpdateStatus(int $id): JsonResponse
    {
        UpdateUserStatusAction::run($id);

        return $this->hxNotify('Estado actualizado')->hxResponse();
    }

    public function asUpdatePermission(Request $request, int $id): JsonResponse
    {
        UpdateUserPermissionAction::run($id, (int) $request->input('permission_id'));

        return $this->hxNotify('Permisos actualizados')->hxResponse();
    }
}
