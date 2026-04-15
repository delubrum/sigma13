<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Response;

final class CreateAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        $assets = Asset::select('id', 'hostname', 'serial', 'sap')
            ->whereIn('area', ['Machinery', 'Vehicles'])
            ->orderBy('hostname', 'ASC')
            ->get();

        return $this->hxView('maintenance::new', [
            'assets' => $assets,
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }
}
