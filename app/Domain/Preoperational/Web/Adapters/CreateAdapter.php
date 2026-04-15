<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        $vehiculos = Asset::query()
            ->where('area', 'Vehicles')
            ->orderBy('hostname', 'asc')
            ->get();

        return $this->hxView('preoperational::new', [
            'vehiculos' => $vehiculos,
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }
}
