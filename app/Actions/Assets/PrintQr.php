<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Data\Assets\Sidebar;
use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PrintQr
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::findOrFail($id);
        $data = Sidebar::fromModel($asset);

        return view('assets.print-qr', ['data' => $data]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}
