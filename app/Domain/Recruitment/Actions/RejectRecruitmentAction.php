<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\Recruitment;
use Lorisleiva\Actions\Concerns\AsAction;

final class RejectRecruitmentAction
{
    use AsAction;

    public function handle(int $id, string $reason): void
    {
        /** @var Recruitment $recruitment */
        $recruitment = Recruitment::query()->findOrFail($id);
        $recruitment->status = 'rejected';
        $recruitment->rejection = $reason;
        $recruitment->save();
    }
}
