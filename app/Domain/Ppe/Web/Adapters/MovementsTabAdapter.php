<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters;

use App\Domain\Ppe\Actions\GetPpeMovementsDataAction;
use App\Domain\Ppe\Data\PpeMovementTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Actions\SubTableAdapter;

/** @extends SubTableAdapter<PpeMovementTableData> */
final class MovementsTabAdapter extends SubTableAdapter
{
    #[\Override]
    protected function tabConfig(): Config
    {
        return new Config(
            title: 'Movimientos',
            icon: 'ri-swap-line',
            subtitle: 'Ingresos y salidas del ítem',
            columns: SchemaGenerator::toColumns(PpeMovementTableData::class),
        );
    }

    #[\Override]
    protected function tabRoute(): string
    {
        return 'ppe-entries.tab.movements';
    }

    #[\Override]
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        return GetPpeMovementsDataAction::run(
            itemId:  $parentId,
            filters: [],
            sorts:   [],
            page:    $page,
            size:    $size,
        );
    }
}
