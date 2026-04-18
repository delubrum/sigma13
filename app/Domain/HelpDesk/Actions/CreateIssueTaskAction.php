<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Actions;

use App\Domain\HelpDesk\Data\TaskUpsertData;
use App\Domain\HelpDesk\Models\Issue;
use App\Domain\HelpDesk\Models\IssueItem;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateIssueTaskAction
{
    use AsAction;

    public function handle(TaskUpsertData $data): IssueItem
    {
        $issueId = (int) $data->issue_id;
        $userId  = (int) Auth::id();

        $issue = Issue::findOrFail($issueId);

        // Only the assignee may add tasks; if unassigned, the first technician self-assigns
        if ($issue->assignee_id !== null && $issue->assignee_id !== $userId) {
            abort(403, 'Solo el técnico asignado puede agregar tareas a este ticket.');
        }

        $item = IssueItem::create([
            'issue_id'         => $issueId,
            'user_id'          => $userId,
            'complexity'       => $data->complexity,
            'action_taken'     => $data->action_taken,
            'duration_minutes' => (int) $data->duration_minutes,
            'notes'            => $data->notes,
        ]);

        $updates = ['status' => 'Started', 'started_at' => now()];
        if ($issue->assignee_id === null) {
            $updates['assignee_id'] = $userId;
        }

        Issue::where('id', $issueId)
            ->where('status', 'Open')
            ->update($updates);

        return $item;
    }
}
