<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Actions\Tabs\Details as GetDetails;
use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Asset $asset): Response
    {
        $asset->load('currentAssignment.employee');

        return $this->hxView('assets::tabs.details', [
            'asset' => $asset,
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }
}
