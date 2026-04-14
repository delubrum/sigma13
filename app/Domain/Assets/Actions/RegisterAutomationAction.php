<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\AutomationModalData;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterAutomationAction
{
    use AsAction;

    public function handle(int $assetId, AutomationModalData $data): int
    {
        return DB::table('mnt_preventive_form')->insertGetId([
            'asset_id'          => $assetId,
            'activity'          => $data->activity,
            'frequency'         => $data->frequency,
            'last_performed_at' => $data->last_performed_at,
            'status'            => $data->status,
            'kind'              => 'it',
        ]);
    }
}
