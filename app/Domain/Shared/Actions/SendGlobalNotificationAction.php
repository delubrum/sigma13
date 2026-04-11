<?php

declare(strict_types=1);

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Contracts\NotificationChannelData;
use App\Domain\Shared\Data\EmailData;
use App\Domain\Shared\Data\TelegramData;
use App\Domain\Shared\Data\WebPushData;
use App\Mail\GenericMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendGlobalNotificationAction implements ShouldQueue
{
    use AsAction;

    private const array CHANNEL_HANDLERS = [
        EmailData::class => 'deliverEmail',
        TelegramData::class => 'deliverTelegram',
        WebPushData::class => 'deliverWebPush',
    ];

    /**
     * Dispatch polymorphic notifications across multiple channels.
     */
    public function handle(NotificationChannelData ...$notifications): void
    {
        foreach ($notifications as $notification) {
            $handler = self::CHANNEL_HANDLERS[$notification::class] ?? null;

            if ($handler && method_exists($this, $handler)) {
                $this->{$handler}($notification);
            }
        }
    }

    private function deliverEmail(EmailData $data): void
    {
        Mail::to($data->to)->send(new GenericMailable($data));
    }

    private function deliverTelegram(TelegramData $data): void
    {
        // Placeholder: Enviar usando TelegramService (implementación en Principio 15)
        // \App\Domain\Shared\Infrastructure\Telegram\TelegramService::make()->send($data);
    }

    private function deliverWebPush(WebPushData $data): void
    {
        // Placeholder: Enviar usando el sistema de notificaciones de Laravel
        // $users = \App\Domain\Users\Models\User::findMany((array)$data->user_id);
        // Notification::send($users, new \App\Notifications\WebPushNotification($data));
    }
}
