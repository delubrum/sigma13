<?php

declare(strict_types=1);

namespace App\Actions\Password;

use App\Data\Password\Form;
use App\Models\User;
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

        // if (! $user) {
        //     \Log::info("Intento fallido: {$email} no está en la DB.");
        //     return;
        // }

        $token = Password::createToken($user);

        $user->notify(new ResetPasswordMail($token, $email));
    }

    public function asController(Request $request): JsonResponse
    {
        $data = Form::from($request->all());

        $this->handle($data->email);

        return $this
            ->hxNotify('Si el correo existe, recibirás el enlace en breve.', 'success')
            ->hxResponse();
    }
}
