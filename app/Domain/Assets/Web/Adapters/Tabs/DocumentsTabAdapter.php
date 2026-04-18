<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetDocumentsAction;
use App\Domain\Assets\Data\Tabs\DocumentsTableData;
use App\Domain\Assets\Web\Adapters\Modals\DocumentsModalAdapter;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Web\Adapters\AbstractSubTableAdapter;

/** @extends AbstractSubTableAdapter<DocumentsTableData> */
final class DocumentsTabAdapter extends AbstractSubTableAdapter
{
    public function __construct(private readonly DocumentsModalAdapter $documentConfig) {}

    public function config(): Config
    {
        return $this->documentConfig->config();
    }

    protected function route(): string
    {
        return 'assets.documents';
    }

    /** @return PaginatedResult<DocumentsTableData> */
    protected function getData(int $parentId, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<DocumentsTableData> $result */
        return GetAssetDocumentsAction::run(assetId: $parentId, page: $page, size: $size);
    }
}
