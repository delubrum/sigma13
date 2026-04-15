<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveJobProfileItemAction
{
    use AsAction;

    /**
     * @param  array<mixed>  $data
     */
    public function handle(int $jpId, string $kind, array $data, int $userId): bool
    {
        $content = json_encode($data);

        $existing = DB::table('job_profile_items')
            ->where('jp_id', $jpId)
            ->where('kind', $kind)
            ->first();

        if ($existing !== null) {
            DB::table('job_profile_items')
                ->where('id', $existing->id)
                ->update(['content' => $content, 'user_id' => $userId]);
        } else {
            DB::table('job_profile_items')->insert([
                'jp_id' => $jpId,
                'kind' => $kind,
                'content' => $content,
                'user_id' => $userId,
            ]);
        }

        return true;
    }
}
