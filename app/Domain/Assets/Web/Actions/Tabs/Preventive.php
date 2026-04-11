<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Preventive
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $asset = Asset::findOrFail($id);
        return $this->hxView('assets::tabs.preventive', ['asset' => $asset]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
