<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters\Modals;

use App\Domain\Recruitment\Actions\GetCandidatesDataAction;
use App\Domain\Recruitment\Actions\UpsertCandidateAction;
use App\Domain\Recruitment\Data\CandidateTableData;
use App\Domain\Shared\Data\PaginatedResult;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class CandidateModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asCreate(int $recruitmentId): Response
    {
        return $this->hxView('recruitment::modals.candidate', [
            'recruitmentId' => $recruitmentId,
            'candidate' => null,
        ]);
    }

    public function asEdit(int $recruitmentId, int $candidateId): Response
    {
        $candidate = DB::table('recruitment_candidates')->where('id', $candidateId)->first();

        return $this->hxView('recruitment::modals.candidate', [
            'recruitmentId' => $recruitmentId,
            'candidate' => $candidate,
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $result = UpsertCandidateAction::run($request->all());

        if ($result['duplicate']) {
            return $this->hxNotify('error', 'Candidato ya existe con esa cédula')->hxResponse();
        }

        $rid = $result['candidate']->recruitment_id;

        return $this->hxNotify('success', 'Candidato guardado')
            ->hxRefreshTables(["dt_candidates_{$rid}"])
            ->hxCloseModals(['modal-body-2'])
            ->hxResponse();
    }

    public function destroy(int $candidateId): JsonResponse
    {
        $row = DB::table('recruitment_candidates')->where('id', $candidateId)->first(['recruitment_id']);
        $rid = $row ? (int) $row->recruitment_id : 0;
        DB::table('recruitment_candidates')->where('id', $candidateId)->delete();

        return $this->hxNotify('success', 'Candidato eliminado')
            ->hxRefreshTables(["dt_candidates_{$rid}"])
            ->hxResponse();
    }

    public function asData(int $recruitmentId, Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var PaginatedResult<CandidateTableData> $result */
        $result = GetCandidatesDataAction::run(
            recruitmentId: $recruitmentId,
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15),
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }
}
