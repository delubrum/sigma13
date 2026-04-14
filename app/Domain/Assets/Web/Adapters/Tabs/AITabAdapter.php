<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class AITabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        return $this->hxView('assets::tabs.ai', ['assetId' => $id]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }

    public function asGenerate(int $asset): Response
    {
        $this->hxNotify('Análisis AI en construcción', 'info');

        return $this->hxResponse();
    }
}
