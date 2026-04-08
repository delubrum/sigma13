<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
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
