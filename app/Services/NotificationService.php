<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Jobs\SendTelegramJob;
use App\Mail\ResetPasswordMail;
use Illuminate\Mail\Mailable;

class NotificationService
{
    public function email(string $to, Mailable $mailable): void
    {
        dispatch(new SendEmailJob($to, $mailable));
    }

    /** @param array<string, mixed>|null $buttons */
    public function telegram(string $message, ?array $buttons = null): void
    {
        dispatch(new SendTelegramJob($message, $buttons));
    }

    public function passwordReset(string $email, string $token): void
    {
        $this->email(
            $email,
            new ResetPasswordMail($token, $email)
        );

        $this->telegram(
            "🔐 <b>Solicitud de recuperación de contraseña</b>\n\n▸ Email: {$email}\n▸ Hora: ".now()->format('H:i')
        );
    }
}
