<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Data\Sidebar;
use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PrintQr
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Asset $asset): \Illuminate\View\View
    {
        $data = Sidebar::from($asset);

        return view('assets::print-qr', ['data' => $data]);
    }

    public function asController(Asset $asset): \Illuminate\View\View
    {
        return $this->handle($asset);
    }
}
