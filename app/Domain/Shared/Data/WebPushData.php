<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use App\Domain\Shared\Contracts\NotificationChannelData;
use Spatie\LaravelData\Data;

final class WebPushData extends Data implements NotificationChannelData
{
    public function __construct(
        /** @var int|array<int, int> */
        public int|array $user_id,
        public string $title,
        public string $body,
        public ?string $url = null,
        public ?string $icon = null,
    ) {}
}
