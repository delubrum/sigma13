<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Models\Asset;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PublicShow
{
    use AsAction;

    public function handle(string $serial): Response
    {
        $asset = is_numeric($serial) 
            ? Asset::with(['currentAssignment.employee', 'media'])->find($serial)
            : Asset::with(['currentAssignment.employee', 'media'])->where('serial', $serial)->first();

        if (! $asset) {
            abort(404, 'Activo no encontrado');
        }

        // Obtener últimos 3 correctivos (Tabla mnt)
        $correctives = \Illuminate\Support\Facades\DB::table('mnt')
            ->where('asset_id', $asset->id)
            ->whereNotNull('ended_at')
            ->orderByDesc('ended_at')
            ->limit(3)
            ->get();

        // Obtener últimos 3 preventivos (Tabla mnt_preventive_form)
        $preventives = \Illuminate\Support\Facades\DB::table('mnt_preventive_form')
            ->where('asset_id', $asset->id)
            ->whereNotNull('last_performed_at')
            ->orderByDesc('last_performed_at')
            ->limit(3)
            ->get();

        return response()->view('assets::public-show', [
            'asset' => $asset,
            'correctives' => $correctives,
            'preventives' => $preventives,
        ]);
    }
}
