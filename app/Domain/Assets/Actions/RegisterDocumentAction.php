<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\DocumentModalData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetDocument;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterDocumentAction
{
    use AsAction;

    public function handle(int $assetId, DocumentModalData $data, ?UploadedFile $file): AssetDocument
    {
        Asset::query()->findOrFail($assetId);

        /** @var AssetDocument $doc */
        $doc = AssetDocument::create([
            'asset_id' => $assetId,
            'name' => $data->name,
            'code' => $data->code,
            'expiry' => $data->expiry,
        ]);

        if ($file instanceof UploadedFile) {
            $doc->addMedia($file)->toMediaCollection('documents');
        }

        return $doc;
    }
}
