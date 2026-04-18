<?php

declare(strict_types=1);

namespace App\Domain\Stock\Web\Adapters;

use App\Domain\Stock\Actions\GetStockDataAction;
use App\Domain\Stock\Actions\GetStockSidebarAction;
use App\Domain\Stock\Data\TableData;
use App\Domain\Stock\Data\UpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Adapters\AbstractIndexAdapter;

final class IndexAdapter extends AbstractIndexAdapter implements \App\Contracts\HasEditDetail
{
    protected function route(): string
    {
        return 'stock';
    }

    public function config(): Config
    {
        $kind = request('kind', 'EPP');
        
        return new Config(
            title:          "{$kind}",
            icon:           'ri-stack-line',
            subtitle:       'Catálogo Maestro de Productos',
            newButtonLabel: 'Nuevo Artículo',
            modalWidth:     '40',
            multipart:      true,
            columns:        SchemaGenerator::toColumns(TableData::class),
            formFields:     SchemaGenerator::toFields(UpsertData::class),
        );
    }

    protected function getData(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        // Force kind filter from URL if present
        if ($kind = request('kind')) {
            $filters['kind'] = $kind;
        }

        return GetStockDataAction::run(filters: $filters, sorts: $sorts, page: $page, size: $size);
    }
}
