<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Web\Adapters;

use App\Domain\JobProfiles\Actions\SaveJobProfileResourceAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveResourceAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(Request $request): Response
    {
        $jpId = (int) $request->input('id', 0);
        $group = (string) $request->input('group', '');
        $value = trim((string) $request->input('value', ''));
        $isInput = in_array($request->input('is_input'), ['true', '1', 1, true], true);

        if ($jpId === 0 || $group === '') {
            abort(422);
        }

        SaveJobProfileResourceAction::run($jpId, $group, $value, $isInput, (int) Auth::id());

        return $this->hxNotify('success', 'Actualizado')->hxResponse();
    }
}
