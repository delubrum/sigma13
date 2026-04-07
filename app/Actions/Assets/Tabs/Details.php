<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Details
{
    use AsAction;

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
