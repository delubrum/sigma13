<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details
{
    use AsAction;
    use HtmxOrchestrator;

    /**
     * Web adapter to show the Details tab of an asset.
     */
    public function handle(int $id): Response
    {
        $asset = Asset::with(['category', 'brand', 'model', 'currentAssignment.employee'])->findOrFail($id);

        return $this->hxView('assets::tabs.details', [
            'asset' => $asset,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
