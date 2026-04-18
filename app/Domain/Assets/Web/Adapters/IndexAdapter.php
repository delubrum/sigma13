<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasOptions;
use App\Domain\Assets\Actions\GetAssetsDataAction;
use App\Domain\Assets\Actions\GetAssetSidebarAction;
use App\Domain\Assets\Data\ActionsData;
use App\Domain\Assets\Data\SidebarData;
use App\Domain\Assets\Data\TableData;
use App\Domain\Assets\Data\TabsData;
use App\Domain\Assets\Data\UpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Adapters\AbstractIndexAdapter;
use Illuminate\Support\Facades\DB;

final class IndexAdapter extends AbstractIndexAdapter implements HasDetail, HasOptions
{
    protected function route(): string
    {
        return 'assets';
    }

    public function config(): Config
    {
        return new Config(
            title:          'Activos',
            icon:           'ri-stack-line',
            subtitle:       'Registro de activos tecnológicos',
            newButtonLabel: 'Nuevo Activo',
            modalWidth:     '90',
            columns:        SchemaGenerator::toColumns(TableData::class),
            formFields:     SchemaGenerator::toFields(UpsertData::class),
            tabs:           SchemaGenerator::toTabs(TabsData::class),
            options:        SchemaGenerator::toOptions(ActionsData::class),
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetAssetSidebarAction::run($id);

        return $result;
    }

    public function resolveOptions(string $key, array $params): array
    {
        return match ($key) {
            'assets' => DB::table('assets')
                ->select('id', 'hostname', 'serial', 'sap', 'area')
                ->orderBy('hostname')
                ->when(isset($params['area']), fn ($q) => $q->whereIn('area', explode(',', $params['area'])))
                ->get()
                ->map(fn (object $a): array => [
                    'group' => $a->area === 'Locative' ? 'Locative' : 'Machinery',
                    'value' => $a->id,
                    'label' => mb_convert_case(
                        implode(' | ', array_filter([$a->hostname, $a->serial, $a->sap])),
                        MB_CASE_TITLE, 'UTF-8'
                    ),
                ])->all(),
            default => [],
        };
    }

    protected function getData(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<TableData> $result */
        $result = GetAssetsDataAction::run(filters: $filters, sorts: $sorts, page: $page, size: $size);

        return $result;
    }
}
