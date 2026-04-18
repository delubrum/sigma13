<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateUserStatusAction
{
    use AsAction;

    public function handle(int $id): bool
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        return $user->is_active;
    }
}
