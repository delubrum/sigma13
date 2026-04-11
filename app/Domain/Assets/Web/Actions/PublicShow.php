<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Models\Asset;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PublicShow
{
    use AsAction;

    public function handle(string $serial): Response
    {
        /** @var Asset $asset */
        $asset = Asset::query()
            ->with([
                'currentAssignment.employee', 
                'media',
                'correctives' => fn($q) => $q->limit(3),
                'preventives' => fn($q) => $q->limit(3)
            ])
            ->when(is_numeric($serial), 
                fn($q) => $q->where('id', (int) $serial),
                fn($q) => $q->where('serial', $serial)
            )
            ->firstOrFail();

        return response()->view('assets::public-show', [
            'asset'       => $asset,
            'correctives' => $asset->correctives,
            'preventives' => $asset->preventives,
        ]);
    }
}