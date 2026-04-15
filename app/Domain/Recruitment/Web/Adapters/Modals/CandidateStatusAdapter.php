<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters\Modals;

use App\Domain\Recruitment\Actions\UpdateCandidateStatusAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class CandidateStatusAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $candidateId, Request $request): JsonResponse
    {
        $field = (string) $request->input('field', 'status');
        $value = (string) $request->input('value', '');

        $result = UpdateCandidateStatusAction::run($candidateId, $field, $value);

        if (! $result['ok']) {
            $missing = implode(', ', array_slice($result['missing'], 0, 3));
            $suffix = count($result['missing']) > 3 ? '...' : '';

            return $this->hxNotify('error', "Faltan datos: {$missing}{$suffix}")->hxResponse();
        }

        $rid = (int) DB::table('recruitment_candidates')->where('id', $candidateId)->value('recruitment_id');

        return $this->hxNotify('success', 'Estado actualizado')
            ->hxRefreshTables(["dt_candidates_{$rid}"])
            ->hxResponse();
    }
}
