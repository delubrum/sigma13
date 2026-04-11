<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Actions\Modals\Automation as AutomationConfig;
use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automations
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $asset = Asset::findOrFail($id);
        
        // This is a TabIndex, so it uses the same pattern as Index but for a relation
        return $this->hxView('components::tab-index', [
            'route' => "assets/{$id}/automations",
            'config' => app(AutomationConfig::class)->config(),
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
