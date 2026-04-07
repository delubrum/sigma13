<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Documents
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $asset = Asset::findOrFail($id);

        return view('assets.tabs.documents', ['asset' => $asset]);
    }
}
