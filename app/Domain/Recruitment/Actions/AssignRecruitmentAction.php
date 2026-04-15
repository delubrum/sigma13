<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class AssignRecruitmentAction
{
    use AsAction;

    public function handle(int $id, int $assigneeId): void
    {
        /** @var Recruitment $recruitment */
        $recruitment = Recruitment::query()->findOrFail($id);
        $recruitment->assignee_id = $assigneeId;
        $recruitment->save();
    }

    /** @return array<object> */
    public static function allUsers(): array
    {
        return DB::table('users')->orderBy('username')->get(['id', 'username'])->all();
    }
}
