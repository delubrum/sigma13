<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Shared\Events\PasswordResetRequested;
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
        PasswordResetRequested::dispatch($user);
        
        return $user;
    }
}
