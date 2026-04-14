<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Data;

use App\Domain\Notifications\Contracts\NotificationChannelData;
use Spatie\LaravelData\Data;

final class TelegramData extends Data implements NotificationChannelData
{
    public function __construct(
        /** @var string|array<int, string|int> */
        public string|array $chat_id,
        public string $text,
        /** @var array<string, string>|null */
        public ?array $buttons = null,
        public string $parse_mode = 'HTML',
        public ?string $image_url = null,
    ) {}
}
