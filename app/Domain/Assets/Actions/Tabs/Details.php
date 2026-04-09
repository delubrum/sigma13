<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Tabs;

use App\Contracts\HasModule;
use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details implements HasModule
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            title: 'Detalles del Activo',
            subtitle: '',
            icon: 'ri-information-line',
            columns: [],
            formFields: []
        );
    }

    public function handle(int $id): View
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        return view('assets.tabs.details', [
            'asset' => $asset,
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}
