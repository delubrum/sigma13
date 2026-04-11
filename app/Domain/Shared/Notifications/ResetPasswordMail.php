<?php

declare(strict_types=1);

namespace App\Domain\Shared\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordMail extends Notification implements ShouldQueue
{
    use Queueable;

    // Pasamos el email también al constructor para asegurar que viaje en la cola
    public function __construct(
        public string $token,
        public string $email
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Restablecer contraseña - SIGMA')
            ->view('emails.reset-password', [
                'token' => $this->token,
                'email' => $this->email, // Usamos la del constructor, no la del modelo
                'user' => $notifiable,
            ]);
    }
}
