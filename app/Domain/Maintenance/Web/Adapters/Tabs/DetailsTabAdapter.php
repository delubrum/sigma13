<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters\Tabs;

use App\Domain\Maintenance\Models\Maintenance;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DetailsTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $mnt = Maintenance::with(['user', 'asset'])->findOrFail($id);
        
        /** @var \App\Domain\Users\Models\User $user */
        $user = auth()->user();
        $permissions = (array) json_decode($user->permissions ?? '[]', true);
        $canClose = !empty(array_intersect(['44'], $permissions));
        
        $assets = \App\Domain\Assets\Models\Asset::select('id', 'hostname', 'serial', 'sap')
            ->orderBy('hostname', 'ASC')
            ->get();
            
        $users = \App\Domain\Users\Models\User::select('id', 'username')
            ->where('active', true)
            ->where('permissions', 'LIKE', '%"35"%')
            ->orderBy('username', 'ASC')
            ->get();

        return $this->hxView('maintenance::tabs.details', [
            'id' => $mnt,
            'canClose' => $canClose,
            'assets' => $assets,
            'users' => $users,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
