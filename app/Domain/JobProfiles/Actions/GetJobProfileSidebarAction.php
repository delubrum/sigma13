<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Actions;

use App\Domain\JobProfiles\Data\SidebarData;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetJobProfileSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $row = DB::table('job_profiles as a')
            ->select(
                'a.*',
                DB::raw('(SELECT name FROM hr_db WHERE id = a.reports_to LIMIT 1) as reports_to_name'),
                DB::raw('(SELECT name FROM hr_db WHERE id = a.division_id LIMIT 1) as division_name'),
                DB::raw('(SELECT area FROM hr_db WHERE id = a.division_id LIMIT 1) as area_name'),
            )
            ->where('a.id', $id)
            ->first();

        if ($row === null) {
            abort(404);
        }

        // Resolve names for subordinate positions (reports JSON array of hr_db IDs)
        $reportsNames = '';
        if (! empty($row->reports)) {
            $reportIds = json_decode((string) $row->reports, true);
            if (is_array($reportIds) && count($reportIds) > 0) {
                $names = DB::table('hr_db')
                    ->whereIn('id', $reportIds)
                    ->pluck('name')
                    ->toArray();
                $reportsNames = implode(', ', $names);
            }
        }

        return new SidebarData(
            id: (int) $row->id,
            code: (string) ($row->code ?? ''),
            name: (string) ($row->name ?? ''),
            division: (string) ($row->division_name ?? ''),
            area: (string) ($row->area_name ?? ''),
            reportsTo: (string) ($row->reports_to_name ?? ''),
            reportsList: $reportsNames,
            workMode: (string) ($row->work_mode ?? ''),
            rank: (string) ($row->rank ?? ''),
            schedule: (string) ($row->schedule ?? ''),
            travel: (string) ($row->travel ?? ''),
            relocation: (string) ($row->relocation ?? ''),
            lang: (string) ($row->lang ?? ''),
            experience: (string) ($row->experience ?? ''),
            obs: (string) ($row->obs ?? ''),
            mission: (string) ($row->mission ?? ''),
            createdAt: isset($row->created_at) ? substr((string) $row->created_at, 0, 10) : '',
            canEdit: true,
        );
    }
}
