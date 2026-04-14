<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Web\Controllers;

use App\Domain\Notifications\Actions\SendGlobalNotificationAction;
use App\Domain\Notifications\Data\TelegramData;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class TelegramWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $message = $request->input('message');

        if (! isset($message['text']) || ! str_starts_with($message['text'], '/start ')) {
            return response('OK', 200);
        }

        $token = str_replace('/start ', '', $message['text']);
        $chatId = (string) $message['chat']['id'];

        $user = User::where('telegram_link_token', $token)->first();

        if ($user) {
            $user->update([
                'telegram_chat_id' => $chatId,
                'telegram_link_token' => null,
            ]);

            // Bienvenida
            SendGlobalNotificationAction::dispatch(
                new TelegramData(
                    chat_id: $chatId,
                    text: "🤝 <b>¡Hola, {$user->name}!</b>\n\nTu cuenta ha sido vinculada correctamente. A partir de ahora recibirás notificaciones importantes por aquí.\n\n¡Bienvenido a SIGMA!",
                    parse_mode: 'HTML'
                )
            );
        }

        return response('OK', 200);
    }
}
