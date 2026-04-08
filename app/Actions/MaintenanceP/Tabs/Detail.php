<?php

declare(strict_types=1);

namespace App\Actions\MaintenanceP\Tabs;

use App\Models\MntPreventive;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $ticket = MntPreventive::with(['forms', 'asset'])->findOrFail($id);

        return view('maintenancep.tabs.info', compact('id', 'ticket'));
    }
}
