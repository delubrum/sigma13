<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Actions;

use App\Domain\HelpDesk\Data\UpsertData;
use App\Domain\HelpDesk\Models\Issue;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateIssueAction
{
    use AsAction;

    public function handle(UpsertData $data): Issue
    {
        $issue = Issue::create([
            'kind'        => $data->kind,
            'facility'    => $data->facility,
            'asset_id'    => $data->asset_id,
            'priority'    => $data->priority,
            'description' => $data->description,
            'reporter_id' => Auth::id(),
            'status'      => 'Open',
        ]);

        return $issue;
    }
}
