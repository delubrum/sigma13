<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Domain\Maintenance\Models\Mnt;
use App\Domain\Users\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $ticket = Mnt::with(['user', 'assignee', 'asset'])->findOrFail($id);

        // Maquinaria + vehículos
        $assets = Asset::whereIn('area', ['Machinery', 'Vehicles'])
            ->orderBy('hostname')
            ->get(['id', 'hostname', 'serial', 'sap']);

        // Técnicos con permiso 35 (Maintenance)
        $technicians = User::where('is_active', true)
            ->whereJsonContains('permissions', '35')
            ->orderBy('name')
            ->get(['id', 'name']);

        /** @var view-string $view */
        $view = 'maintenance.tabs.info';

        return view($view, ['id' => $id, 'ticket' => $ticket, 'assets' => $assets, 'technicians' => $technicians]);
    }
}
