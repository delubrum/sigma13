<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions\Password;

use App\Domain\Users\Data\UserForm;
use App\Domain\Users\Models\User;
use App\Notifications\ResetPasswordMail;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendResetLink
{
    use AsAction;
    use HtmxOrchestrator;

    /**
     * Ya no necesitamos el constructor con NotificationService.
     * Al ser una Action de Loris Leiva, podemos dejarlo vacío o borrarlo.
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

    public function asController(Request $request): JsonResponse
    {
        $email = $request->input('email');

        if (! is_string($email) || blank($email)) {
            return $this->hxNotify('El correo es obligatorio.', 'error')->hxResponse();
        }

        $this->handle($email);

        return $this
            ->hxNotify('Si el correo existe, recibirás el enlace en breve.', 'success')
            ->hxResponse();
    }
}
