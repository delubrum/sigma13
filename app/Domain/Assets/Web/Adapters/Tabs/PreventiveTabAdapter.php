<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Actions\GetAssetPreventiveAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PreventiveTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $automations = GetAssetPreventiveAction::run($id);

        return $this->hxView('assets::tabs.preventive', ['automations' => $automations]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
