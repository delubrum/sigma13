<?php

declare(strict_types=1);

namespace App\Actions\Users\Tabs;

use App\Models\Permission;
use App\Models\User;
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

        return view('users.tabs.detail', compact('user', 'permissions', 'userPermissions'));
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
        $permissionId = (int) $request->input('permission_id');
        
        $current = $user->permissions ?? [];
        
        if (in_array($permissionId, $current, true)) {
            $current = array_values(array_filter($current, fn($id) => $id !== $permissionId));
        } else {
            $current[] = $permissionId;
        }

        $user->update(['permissions' => $current]);

        $this->hxNotify('Permisos actualizados');
        return $this->hxResponse();
    }
}
