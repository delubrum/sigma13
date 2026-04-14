<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters;

use App\Domain\Assets\Actions\GetAssetSidebarAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class SidebarPhotoAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $asset): Response
    {
        $data = GetAssetSidebarAction::run($asset);

        return $this->hxView('assets::sidebar-photo', ['data' => $data]);
    }

    public function asController(int $asset): Response
    {
        return $this->handle($asset);
    }
}
