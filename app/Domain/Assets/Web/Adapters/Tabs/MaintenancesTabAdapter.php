<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetMaintenancesAction;
use App\Domain\Assets\Data\Tabs\MaintenancesTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use Lorisleiva\Actions\Concerns\AsAction;

final class MaintenancesTabAdapter extends SubTableAdapter
{
    use AsAction;

    protected function tabConfig(): Config
    {
        return new Config(
            title:   'Mantenimientos Correctivos',
            icon:    'ri-tools-line',
            columns: SchemaGenerator::toColumns(MaintenancesTableData::class),
        );
    }

    protected function tabRoute(): string
    {
        return 'assets.maintenances';
    }

    /** @return PaginatedResult<MaintenancesTableData> */
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        return GetAssetMaintenancesAction::run(
            assetId: $parentId,
            page:    $page,
            size:    $size,
        );
    }
}
