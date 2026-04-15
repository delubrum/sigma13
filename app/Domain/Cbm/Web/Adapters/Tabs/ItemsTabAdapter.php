<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Web\Adapters\Tabs;

use App\Domain\Cbm\Models\Cbm;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class ItemsTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $cbm = Cbm::with('items')->findOrFail($id);

        return $this->hxView('cbm::tabs.items', [
            'model' => $cbm,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
