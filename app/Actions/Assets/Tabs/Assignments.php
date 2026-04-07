<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\AssetEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

final class Assignments
{
    use AsAction;

    public function handle(int $id): View
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        $assignments = AssetEvent::query()
            ->where('asset_id', $id)
            ->where('kind', 'assignment')
            ->with(['employee'])
            ->orderByDesc('id')
            ->get()
            ->map(function (AssetEvent $event): array {
                $date = $event->created_at instanceof Carbon
                    ? $event->created_at->format('d/m/Y H:i')
                    : '---';

                return [
                    'id' => $event->id,
                    'date' => $date,
                    'assignee' => $event->employee->name ?? '---',
                    'hardware' => is_array($event->hardware) ? implode(', ', $event->hardware) : ($event->hardware ?? '---'),
                    'software' => is_array($event->software) ? implode(', ', $event->software) : ($event->software ?? '---'),
                    'minute' => $event->notes ? '<span class="text-emerald-600"><i class="ri-file-text-line"></i> Adjunto</span>' : '<span class="opacity-30">---</span>',
                    'asset_id' => $event->asset_id,
                ];
            });

        return view('assets.tabs.assignments', [
            'asset' => $asset,
            'assignments' => $assignments,
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }
}
