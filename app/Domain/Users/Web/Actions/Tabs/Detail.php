<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Actions\Tabs;

use App\Domain\Users\Data\UpdateField;
use App\Domain\Users\Models\Permission;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    /** GET /users/{id}/info */
    public function asController(Request $request, string $id): Response
    {
        $user = User::findOrFail($id);

        // Agrupar permisos por categoría
        $permissions = Permission::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $userPermissions = $user->permissions ?? [];

        return $this->hxView('users::tabs.detail', ['user' => $user, 'permissions' => $permissions, 'userPermissions' => $userPermissions]);
    }

    /** POST /users/{id}/status */
    public function asUpdateStatus(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        $this->hxNotify('Estado actualizado');
        $this->hxRefreshTables(['dt_users']);

        // No cerramos el modal, solo notificamos
        return $this->hxResponse();
    }

    /** POST /users/{id}/permission */
    public function asUpdatePermission(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $permissionId = $request->integer('permission_id');

        /** @var list<int> $current */
        $current = (array) ($user->permissions ?? []);

        if (in_array($permissionId, $current, true)) {
            $current = array_values(array_filter($current, fn ($id): bool => $id !== $permissionId));
        } else {
            $current[] = $permissionId;
        }

        $user->update(['permissions' => $current]);

        $this->hxNotify('Permisos actualizados');

        return $this->hxResponse();
    }

    /** POST /users/{id}/update-field */
    public function asUpdateField(Request $request, string $id): Response|JsonResponse
    {
        $data = UpdateField::from($request->all());
        $user = User::findOrFail($id);

        $user->update([$data->field => $data->value]);

        $this->hxNotify(ucfirst($data->field === 'email' ? 'correo' : 'nombre').' actualizado');

        // Refresh table to reflect changes
        $this->hxRefreshTables(['dt_users']);

        if ($data->field === 'name') {
            $this->hxModalHeader([
                'icon' => 'ri-user-line',
                'title' => "Usuarios · #{$id} · <span class='opacity-50'>{$user->name}</span>",
                'subtitle' => 'Gestión de perfil y permisos'
            ]);
        }

        return $this->hxResponse();
    }
}
