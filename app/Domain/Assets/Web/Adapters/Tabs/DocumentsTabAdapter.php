<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetDocumentsAction;
use App\Domain\Assets\Data\Tabs\DocumentsTableData;
use App\Domain\Assets\Web\Adapters\Modals\DocumentsModalAdapter;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use Lorisleiva\Actions\Concerns\AsAction;

/** @extends SubTableAdapter<DocumentsTableData> */
final class DocumentsTabAdapter extends SubTableAdapter
{
    use AsAction;

    public function __construct(private readonly DocumentsModalAdapter $documentConfig) {}

    protected function tabConfig(): Config
    {
        return $this->documentConfig->config();
    }

    protected function tabRoute(): string
    {
        return 'assets.documents';
    }

    /** @return PaginatedResult<DocumentsTableData> */
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        /** @var PaginatedResult<DocumentsTableData> $result */
        $result = GetAssetDocumentsAction::run(
            assetId: $parentId,
            page: $page,
            size: $size,
        );

        return $result;
    }
}
