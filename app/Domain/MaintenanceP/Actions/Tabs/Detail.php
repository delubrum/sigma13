<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Actions\Tabs;

use App\Domain\Maintenance\Models\MntPreventive;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $ticket = MntPreventive::with(['form', 'asset'])->findOrFail($id);

        /** @var view-string $view */
        $view = 'maintenancep.tabs.info';

        return view($view, ['id' => $id, 'ticket' => $ticket]);
    }
}
