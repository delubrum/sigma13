<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Data\SidebarData;
use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetRecruitmentSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        /** @var Recruitment $recruitment */
        $recruitment = Recruitment::query()->findOrFail($id);

        $creator = DB::table('users')->where('id', $recruitment->user_id)->value('username') ?? '';
        $approver = DB::table('users')->where('email', $recruitment->approver)->value('username') ?? ($recruitment->approver ?? '');
        $assignee = $recruitment->assignee_id
            ? (string) DB::table('users')->where('id', $recruitment->assignee_id)->value('username')
            : null;

        $profile = $recruitment->profile_id
            ? (string) DB::table('job_profiles')->where('id', $recruitment->profile_id)->value('name')
            : null;

        $divisionId = $recruitment->profile_id
            ? DB::table('job_profiles')->where('id', $recruitment->profile_id)->value('division_id')
            : null;

        $division = $divisionId
            ? (string) DB::table('hr_db')->where('id', $divisionId)->value('name')
            : null;

        $area = $divisionId
            ? (string) DB::table('hr_db')->where('id', $divisionId)->value('area')
            : null;

        $hiredCount = (int) Recruitment::query()
            ->where('recruitment.id', $id)
            ->join('recruitment_candidates as rc', 'rc.recruitment_id', '=', 'recruitment.id')
            ->where('rc.status', 'hired')
            ->count('rc.id');

        return SidebarData::fromModel($recruitment, $creator, $approver, $assignee, $profile, $division, $area, $hiredCount);
    }
}
