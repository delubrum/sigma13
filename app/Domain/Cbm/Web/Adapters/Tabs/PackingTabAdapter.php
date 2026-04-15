<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Web\Adapters\Tabs;

use App\Domain\Cbm\Actions\CalculateCbmPackingAction;
use App\Domain\Cbm\Models\Cbm;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PackingTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $cbm = Cbm::findOrFail($id);
        $packingData = CalculateCbmPackingAction::run($id);

        return $this->hxView('cbm::tabs.packing', [
            'model' => $cbm,
            'packingData' => $packingData,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
