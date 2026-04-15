<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Actions;

use App\Domain\Notifications\Contracts\NotificationChannelData;
use App\Domain\Notifications\Data\EmailData;
use App\Domain\Notifications\Data\TelegramData;
use App\Domain\Notifications\Data\WebPushData;
use App\Domain\Notifications\Mail\GenericMailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendGlobalNotificationAction
{
    use AsAction;

    /**
     * Dispatch polymorphic notifications across multiple channels.
     */
    public function handle(NotificationChannelData ...$notifications): void
    {
        foreach ($notifications as $notification) {
            match (true) {
                $notification instanceof EmailData => $this->deliverEmail($notification),
                $notification instanceof TelegramData => $this->deliverTelegram($notification),
                $notification instanceof WebPushData => $this->deliverWebPush($notification),
                default => null,
            };
        }
    }

    private function deliverEmail(EmailData $data): void
    {
        Mail::to($data->to)->send(new GenericMailable($data));
    }

    private function deliverTelegram(TelegramData $data): void
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            Log::error('Telegram Bot Token not set in services configuration.');

            return;
        }

        $endpoint = $data->image_url ? 'sendPhoto' : 'sendMessage';
        $payload = [
            'chat_id' => $data->chat_id,
            'parse_mode' => $data->parse_mode,
        ];

        if ($data->image_url) {
            $payload['photo'] = $data->image_url;
            $payload['caption'] = str_replace('\n', "\n", $data->text);
        } else {
            $payload['text'] = str_replace('\n', "\n", $data->text);
        }

        Http::post("https://api.telegram.org/bot{$token}/{$endpoint}", $payload)->throw();
    }

    private function deliverWebPush(WebPushData $data): void
    {
        Log::info('WebPush notification not implemented', ['data' => $data->toArray()]);
    }
}
