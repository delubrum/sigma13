<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Web\Adapters\Tabs;

use App\Domain\Fasteners\Models\Fastener;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DetailsTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $f = Fastener::findOrFail($id);

        return $this->hxView('fasteners::tabs.details', [
            'data' => (object) ['model' => $f],
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
