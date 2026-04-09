<?php

declare(strict_types=1);

namespace App\Domain\IT\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Domain\IT\Models\It;
use App\Domain\Users\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $ticket = It::with(['requestor', 'assignee', 'asset'])->findOrFail($id);

        // Assets con área IT (para el select inline)
        $assets = Asset::where('area', 'IT')
            ->orderBy('serial')
            ->get(['id', 'hostname', 'serial', 'sap']);

        // Técnicos con permiso 30 (IT)
        $technicians = User::where('is_active', true)
            ->whereJsonContains('permissions', '30')
            ->orderBy('name')
            ->get(['id', 'name']);

        /** @var view-string $view */
        $view = 'it.tabs.info';

        return view($view, ['id' => $id, 'ticket' => $ticket, 'assets' => $assets, 'technicians' => $technicians]);
    }
}
