<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Shared\Events\PasswordResetRequested;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Event;
use Lorisleiva\Actions\Concerns\AsAction;

final class RequestPasswordReset
{
    use AsAction;

    /**
     * Core logic to request a password reset for a user.
     */
    public function handle(string $userId): User
    {
        $user = User::findOrFail($userId);
        Event::dispatch(new PasswordResetRequested($user));

        return $user;
    }
}
