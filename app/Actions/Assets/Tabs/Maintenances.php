<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Maintenances
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $asset = Asset::findOrFail($id);

        return view('assets.tabs.maintenances', ['asset' => $asset]);
    }
}
