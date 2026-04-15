<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\Recruitment;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchRecruitmentAction
{
    use AsAction;

    private const array ALLOWED = ['assignee_id', 'srange', 'start_date', 'cause', 'others', 'replaces'];

    public function handle(int $id, string $field, mixed $value): bool
    {
        if (! in_array($field, self::ALLOWED, true)) {
            return false;
        }

        /** @var Recruitment $recruitment */
        $recruitment = Recruitment::query()->findOrFail($id);
        $recruitment->$field = $value;
        $recruitment->save();

        return true;
    }
}
