<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetMovementsAction;
use App\Domain\Assets\Data\Tabs\MovementsTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use Lorisleiva\Actions\Concerns\AsAction;

final class MovementsTabAdapter extends SubTableAdapter
{
    use AsAction;

    protected function tabConfig(): Config
    {
        return new Config(
            title:   'Historial de Movimientos',
            icon:    'ri-arrow-left-right-line',
            columns: SchemaGenerator::toColumns(MovementsTableData::class),
        );
    }

    protected function tabRoute(): string
    {
        return 'assets.movements';
    }

    /** @return PaginatedResult<MovementsTableData> */
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        return GetAssetMovementsAction::run(
            assetId: $parentId,
            page:    $page,
            size:    $size,
        );
    }
}
