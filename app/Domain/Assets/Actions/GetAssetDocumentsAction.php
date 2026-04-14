<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\DocumentsTableData;
use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetDocumentsAction
{
    use AsAction;

    /** @return PaginatedResult<DocumentsTableData> */
    public function handle(int $assetId, int $page = 1, int $size = 25): PaginatedResult
    {
        $paginator = AssetDocument::query()
            ->where('asset_id', $assetId)
            ->with('media')
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        /** @var list<DocumentsTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (AssetDocument $doc): DocumentsTableData => DocumentsTableData::fromModel($doc))
                ->all()
        );

        return new PaginatedResult(
            items:    $items,
            lastPage: $paginator->lastPage(),
            total:    $paginator->total(),
        );
    }
}
