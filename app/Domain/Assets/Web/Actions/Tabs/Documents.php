<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Web\Actions\Modals\Document as DocumentConfig;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Documents
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        return $this->hxView('components::tab-index', [
            'route' => "assets/{$id}/documents",
            'config' => app(DocumentConfig::class)->config(),
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
