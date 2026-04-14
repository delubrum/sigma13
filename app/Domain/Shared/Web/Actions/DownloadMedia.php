<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadMedia
{
    use AsAction;

    public function handle(int $id): StreamedResponse
    {
        /** @var Media $media */
        $media = Media::findOrFail($id);

        return response()->streamDownload(function () use ($media): void {
            echo file_get_contents($media->getTemporaryUrl(now()->addMinutes(5)));
        }, $media->file_name, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
