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
        $paginator = \App\Domain\Users\Actions\GetUsersTableData::run([
            'sort' => $request->get('sort', []),
            'filter' => $request->get('filter', []),
            'size' => (int) $request->get('size', 15),
        ]);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (User $user) => \App\Domain\Users\Data\Table::from($user))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function asResetPassword(string $id): JsonResponse
    {
        $user = \App\Domain\Users\Actions\RequestPasswordReset::run($id);

        return $this
            ->hxNotify("Correo de restauración enviado a: {$user->email}")
            ->hxResponse();
    }
}
