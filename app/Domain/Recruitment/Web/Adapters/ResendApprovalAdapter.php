<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResendApprovalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(int $id): JsonResponse
    {
        $row = DB::table('recruitment')->where('id', $id)->first(['approver']);
        $approver = $row ? ((string) DB::table('users')->where('email', $row->approver)->value('username') ?: (string) $row->approver) : '';

        // TODO: dispatch SendGlobalNotificationAction with EmailData

        return $this->hxNotify('success', "Aprobación reenviada a {$approver}")
            ->hxCloseModals(['modal-body-2'])
            ->hxResponse();
    }
}
