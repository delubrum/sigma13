<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetDetailsAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DetailsTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $details = GetAssetDetailsAction::run($id);

        return $this->hxView('assets::tabs.details', ['details' => $details]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
