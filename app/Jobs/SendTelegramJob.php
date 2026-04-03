<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param array<string, mixed>|null $buttons */
    public function __construct(
        public string $message,
        public ?array $buttons = null
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $botToken = config('services.telegram.bot_token');

        if (! $botToken) {
            return;
        }

        $token = is_string($botToken) ? $botToken : '';
        $chatId = config('services.telegram.group_id');

        if (! $token || ! $chatId) {
            return;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $this->message,
            'parse_mode' => 'HTML',
        ];

        if ($this->buttons) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $this->buttons,
            ]);
        }

        Http::get("https://api.telegram.org/bot{$token}/sendMessage", $payload);
    }
}
