<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Actions;

use App\Domain\Identity\Actions\Password\SendResetLink;
use App\Domain\Users\Actions\Index as GetConfig;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'users',
            'storageKey' => 'users_v1_fixed',
            'config' => GetConfig::run(),
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $query = User::query();

        // Ordenamiento
        $sorts = $request->get('sort', []);
        foreach ($sorts as $s) {
            $field = $s['field'] === 'isActive' ? 'is_active' : ($s['field'] === 'createdAt' ? 'created_at' : $s['field']);
            $query->orderBy($field, $s['dir']);
        }

        if (empty($sorts)) {
            $query->latest();
        }

        // Filtros básicos
        $filters = $request->get('filter', []);
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;
            if ($field && $value !== null) {
                $dbField = $field === 'isActive' ? 'is_active' : $field;
                if ($dbField === 'is_active') {
                    $query->where($dbField, $value);
                } else {
                    $query->where($dbField, 'ilike', "%{$value}%");
                }
            }
        }

        $paginator = $query->paginate($request->get('size', 15));

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (User $user) => \App\Domain\Users\Data\Table::from($user))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function asResetPassword(string $id): JsonResponse
    {
        /** @var class-string<User> $userModel */
        $userModel = config('auth.providers.users.model');
        $user = $userModel::findOrFail($id);

        \App\Domain\Shared\Events\PasswordResetRequested::dispatch($user);

        return $this
            ->hxNotify("Correo de restauración enviado a: {$user->email}")
            ->hxResponse();
    }
}
