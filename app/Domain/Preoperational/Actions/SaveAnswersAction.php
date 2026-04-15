<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Actions;

use App\Domain\Preoperational\Models\PreoperationalItem;
use Lorisleiva\Actions\Concerns\AsAction;

final class SaveAnswersAction
{
    use AsAction;

    /** @param array<string, mixed> $data */
    public function handle(int $preopId, array $data): void
    {
        $changes = [];

        foreach ($data as $key => $value) {
            if (! preg_match('/^(question|obs)_(\d+)$/', $key, $m)) {
                continue;
            }
            $type = $m[1];
            $q_id = (int) $m[2];

            $val = is_array($value) ? implode(', ', array_map(strval(...), array_filter($value))) : trim((string) $value);

            if ($type === 'question') {
                $changes[$q_id]['answer'] = $val;
                if ($val === 'Bien') {
                    $changes[$q_id]['obs'] = '';
                }
            } else {
                $changes[$q_id]['obs'] = $val;
            }
        }

        foreach ($changes as $q_id => $itemData) {
            PreoperationalItem::updateOrCreate(
                ['preop_id' => $preopId, 'question_id' => $q_id],
                $itemData
            );
        }
    }
}
