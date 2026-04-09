<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Sidebar;
use App\Domain\Assets\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PrintQr
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::findOrFail($id);
        $data = Sidebar::from($asset);

        return view('assets.print-qr', ['data' => $data]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}
