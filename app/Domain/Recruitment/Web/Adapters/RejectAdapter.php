<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Domain\Recruitment\Actions\RejectRecruitmentAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class RejectAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $id, Request $request): JsonResponse
    {
        $reason = (string) $request->input('prompt_value', '');

        RejectRecruitmentAction::run($id, $reason);

        return $this->hxNotify('success', 'Requisición rechazada')
            ->hxRefreshTables(['dt_recruitment'])
            ->hxCloseModals(['modal-body-2'])
            ->hxResponse();
    }
}
