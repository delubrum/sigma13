<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions\Tabs;

use App\Domain\Users\Data\UpdateField;
use App\Domain\Users\Models\Permission;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    /** GET /users/{id}/info */
    public function asController(Request $request, string $id): View
    {
        $user = User::findOrFail($id);

        // Agrupar permisos por categoría
        $permissions = Permission::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        $userPermissions = $user->permissions ?? [];

        return view('users.tabs.detail', ['user' => $user, 'permissions' => $permissions, 'userPermissions' => $userPermissions]);
    }

    /** POST /users/{id}/status */
    public function asUpdateStatus(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        $this->hxNotify('Estado actualizado');

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
        $this->hxRefreshTables(['users-table']);

        if ($data->field === 'name') {
            // OOB update for modal title
            $title = 'Usuarios';
            $oob = '<h1 id="modal-title" hx-swap-oob="true" class="text-xl font-extrabold uppercase tracking-tight" style="color:var(--tx)">'.
                   "{$title} · #{$id} · <span class='opacity-50'>{$user->name}</span></h1>";

            return response($oob)->withHeaders([
                'HX-Trigger' => json_encode($this->hxTriggers),
            ]);
        }

        return $this->hxResponse();
    }
}
