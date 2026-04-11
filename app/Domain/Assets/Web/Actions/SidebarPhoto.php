<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Data\Sidebar;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class SidebarPhoto
{
    use AsAction;

    public function handle(Asset $asset): Response
    {
        $asset->fresh();
        $data = Sidebar::from($asset);

        return response()->view('assets::sidebar-photo', [
            'data' => $data,
        ]);
    }
}
