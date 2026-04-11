<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

final class InitiatePasswordReset
{
    use AsAction;

    /**
     * Core logic to initiate a password reset process.
     * Pure business logic.
     */
    public function handle(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        $token = Password::createToken($user);

        \App\Domain\Shared\Actions\SendGlobalNotificationAction::dispatch(
            new \App\Domain\Shared\Data\EmailData(
                to: $email,
                subject: 'Restablecer Contraseña',
                template: 'identity::emails.reset-password',
                data: [
                    'token' => $token,
                    'email' => $email,
                    'user' => $user
                ]
            )
        );
    }
}
