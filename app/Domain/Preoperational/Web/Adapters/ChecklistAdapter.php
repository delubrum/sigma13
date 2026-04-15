<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Assets\Models\Asset;
use App\Domain\Preoperational\Models\Preoperational;
use App\Domain\Preoperational\Models\PreoperationalQuestion;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class ChecklistAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $vehicle_id): Response
    {
        if ($vehicle_id === 0) {
            return response(
                '<p class="text-center text-gray-400 py-20 font-black uppercase text-xs italic">Seleccione unidad...</p>'
            )->header('HX-Trigger', '{"lockKM": true}');
        }

        $userId = Auth::id() ?? 147; // fallback or auth

        $draft = Preoperational::firstOrCreate(
            ['user_id' => $userId, 'vehicle_id' => $vehicle_id, 'status' => 'draft'],
            ['km' => 0]
        );

        $saved_items = $draft->items->keyBy('question_id');

        /** @var Asset $vehicle */
        $vehicle = Asset::findOrFail($vehicle_id);

        $questions = PreoperationalQuestion::where('kind', $vehicle->kind)
            ->orderBy('sort')
            ->get()
            ->groupBy('category');

        $headers = json_encode([
            'setPreopId' => (string) $draft->id,
            'setKM' => (string) ($draft->km ?? '0'),
        ]);

        return $this->hxView('preoperational::checklist', [
            'id_preop' => (int) $draft->id,
            'checklist_data' => $questions,
            'saved_items' => $saved_items,
            'draft' => $draft,
        ])->header('HX-Trigger', $headers ?: '{}');
    }

    public function asController(Request $request): Response
    {
        return $this->handle((int) $request->input('vehicle_id', 0));
    }
}
