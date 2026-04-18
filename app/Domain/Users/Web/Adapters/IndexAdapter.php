<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasOptions;
use App\Contracts\HasPatch;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Adapters\AbstractIndexAdapter;
use App\Domain\Users\Actions\GetUsersDataAction;
use Illuminate\Support\Facades\DB;
use App\Domain\Users\Actions\GetUserSidebarAction;
use App\Domain\Users\Actions\RequestPasswordReset;
use App\Domain\Users\Data\SidebarData;
use App\Domain\Users\Data\TabsData;
use App\Domain\Users\Data\TableData;
use App\Domain\Users\Data\UpsertData;
use Illuminate\Http\JsonResponse;

final class IndexAdapter extends AbstractIndexAdapter implements HasDetail, HasOptions, HasPatch
{
    protected function route(): string
    {
        return 'users';
    }

    public function config(): Config
    {
        return new Config(
            title:          'Usuarios',
            icon:           'ri-user-settings-line',
            subtitle:       'Gestión de accesos y permisos',
            newButtonLabel: 'Nuevo Usuario',
            modalWidth:     '50',
            columns:        SchemaGenerator::toColumns(TableData::class),
            formFields:     SchemaGenerator::toFields(UpsertData::class),
            tabs:           SchemaGenerator::toTabs(TabsData::class),
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetUserSidebarAction::run($id);

        return $result;
    }

    public function resolveOptions(string $key, array $params): array
    {
        return match ($key) {
            'users' => DB::table('users')
                ->select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn (object $u): array => ['value' => $u->id, 'label' => $u->name])
                ->all(),
            default => [],
        };
    }

    public function patchConfig(int $id): array
    {
        return [
            'table'  => 'users',
            'fields' => [
                'name'     => [],
                'email'    => [],
                'document' => [],
            ],
        ];
    }

    protected function getData(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<TableData> $result */
        $result = GetUsersDataAction::run(filters: $filters, sorts: $sorts, page: $page, size: $size);

        return $result;
    }

    public function asResetPassword(int $id): JsonResponse
    {
        RequestPasswordReset::run((string) $id);

        return $this->hxNotify('Correo de reset enviado')->hxResponse();
    }
}
