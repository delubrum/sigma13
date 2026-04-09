<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions\Tabs;

use App\Domain\Recruitment\Models\Recruitment;
use App\Domain\Users\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;

    public function asController(Request $request, int $id): View
    {
        $recruitment = Recruitment::with(['requestor', 'assignee'])->findOrFail($id);

        $assignees = User::where('is_active', true)
            ->whereJsonContains('permissions', '85') // Assuming 85 is HR/Recruitment
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('recruitment.tabs.detail', ['id' => $id, 'recruitment' => $recruitment, 'assignees' => $assignees]);
    }
}
