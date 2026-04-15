<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Domain\Recruitment\Actions\PatchRecruitmentAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $id, Request $request): JsonResponse
    {
        $field = (string) $request->input('field', '');
        $value = $request->input('value');

        $result = PatchRecruitmentAction::run($id, $field, $value);

        if (! $result) {
            return $this->hxNotify('error', 'Campo no permitido')->hxResponse();

        }

        return $this->hxNotify('success', 'Actualizado')->hxResponse();
    }
}
