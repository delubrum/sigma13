<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Extrusion\Data\DieItemUpsertData;
use App\Domain\Extrusion\Models\DieItem;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class AdminIndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'extrusion-admin',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Extrusion / Admin DB',
            icon: 'ri-list-settings-line',
            subtitle: 'Gestión de categorías y sistemas',
            newButtonLabel: 'New Entry',
            columns: SchemaGenerator::toColumns(\App\Domain\Extrusion\Data\DieItemTableData::class),
            formFields: SchemaGenerator::toFields(DieItemUpsertData::class),
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $paginator = DieItem::query()
            ->orderBy('id', 'desc')
            ->paginate(
                $request->integer('size', 15),
                ['*'], 'page',
                $request->integer('page', 1)
            );

        return response()->json([
            'data'      => $paginator->map(static fn ($r) => \App\Domain\Extrusion\Data\DieItemTableData::fromModel($r))->values(),
            'last_page' => $paginator->lastPage(),
            'last_row'  => $paginator->total(),
        ]);
    }
}
