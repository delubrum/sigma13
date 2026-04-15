<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Actions;

use App\Domain\Improvement\Data\SidebarData;
use App\Domain\Improvement\Models\Improvement;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetImprovementSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        /** @var Improvement $improvement */
        $improvement = Improvement::query()
            ->select(
                'improvements.*',
                DB::raw('(SELECT username FROM users WHERE id = improvements.user_id LIMIT 1) as creator_name'),
                DB::raw('(SELECT username FROM users WHERE id = improvements.responsible_id LIMIT 1) as responsible_name'),
            )
            ->findOrFail($id);

        $allUsers = DB::table('users')
            ->where('is_active', true)
            ->orderBy('username')
            ->get(['id', 'username as name'])
            ->map(fn (object $u): array => ['id' => (int) $u->id, 'name' => (string) $u->name])
            ->values()
            ->all();

        return SidebarData::fromModel($improvement, $allUsers);
    }
}
