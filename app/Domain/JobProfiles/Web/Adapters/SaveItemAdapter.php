<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Web\Adapters;

use App\Domain\JobProfiles\Actions\SaveJobProfileItemAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveItemAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(Request $request): JsonResponse
    {
        $input = $request->json()->all();

        $jpId = (int) ($input['jp_id'] ?? 0);
        $kind = (string) ($input['type'] ?? '');
        $data = (array) ($input['data'] ?? []);

        if ($jpId === 0 || $kind === '') {
            return response()->json(['status' => 'error', 'message' => 'Invalid input'], 422);
        }

        SaveJobProfileItemAction::run($jpId, $kind, $data, (int) Auth::id());

        return response()->json(['status' => 'success', 'message' => 'Saved']);
    }
}
