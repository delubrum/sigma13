<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveJobProfileResourceAction
{
    use AsAction;

    public function handle(int $jpId, string $group, string $value, bool $isInput, int $userId): bool
    {
        $row = DB::table('job_profile_items')
            ->where('jp_id', $jpId)
            ->where('kind', 'Recursos')
            ->first();

        $data = $row ? json_decode((string) $row->content, true) : [];

        if (! is_array($data)) {
            $data = [];
        }

        // Migrate legacy flat array format
        if (array_values($data) === $data) {
            $data = ['items' => $data];
        }

        // Ensure group structure
        if (! isset($data[$group]) || ! is_array($data[$group])) {
            $data[$group] = ['items' => [], 'otro' => ''];
        } elseif (! isset($data[$group]['items']) || ! is_array($data[$group]['items'])) {
            $data[$group]['items'] = [];
        }

        if ($isInput) {
            if ($value === '') {
                unset($data[$group]['otro']);
            } else {
                $data[$group]['otro'] = $value;
            }
        } elseif ($value !== '') {
            if (! in_array($value, $data[$group]['items'], true)) {
                $data[$group]['items'][] = $value;
            } else {
                $data[$group]['items'] = array_values(
                    array_filter($data[$group]['items'], static fn (string $i): bool => $i !== $value)
                );
            }
        }

        $content = json_encode($data);

        if ($row !== null) {
            DB::table('job_profile_items')
                ->where('id', $row->id)
                ->update(['content' => $content, 'user_id' => $userId]);
        } else {
            DB::table('job_profile_items')->insert([
                'jp_id' => $jpId,
                'kind' => 'Recursos',
                'content' => $content,
                'user_id' => $userId,
            ]);
        }

        return true;
    }
}
