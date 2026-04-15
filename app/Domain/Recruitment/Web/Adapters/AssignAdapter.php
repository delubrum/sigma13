<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Domain\Recruitment\Actions\AssignRecruitmentAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class AssignAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $id): Response
    {
        $assigneeId = (int) DB::table('recruitment')->where('id', $id)->value('assignee_id');
        $users = AssignRecruitmentAction::allUsers();

        return $this->hxView('recruitment::modals.assign', [
            'recruitmentId' => $id,
            'assigneeId' => $assigneeId,
            'users' => $users,
        ]);
    }

    public function save(int $id, Request $request): JsonResponse
    {
        AssignRecruitmentAction::run($id, $request->integer('assignee_id'));

        return $this->hxNotify('success', 'Reclutador asignado')
            ->hxRefreshTables(['dt_recruitment'])
            ->hxCloseModals(['modal-body-2'])
            ->hxResponse();
    }
}
