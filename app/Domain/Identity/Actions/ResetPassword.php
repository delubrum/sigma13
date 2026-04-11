<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Contracts\CanResetPasswordContract;
use App\Domain\Identity\Data\ResetData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResetPassword
{
    use AsAction;

    /**
     * Core business logic for resetting a password.
     * Pure and infra-agnostic. No references to HTTP or HTMX.
     */
    public function handle(ResetData $data): string
    {
        $status = Password::reset(
            credentials: [
                'email' => $data->email,
                'token' => $data->token,
                'password' => $data->password,
                'password_confirmation' => $data->password_confirmation,
            ],
            callback: function (CanResetPasswordContract $user, string $password): void {
                $user->updatePassword(Hash::make($password));
            },
        );

        return is_scalar($status) ? (string) $status : '';
    }
}
