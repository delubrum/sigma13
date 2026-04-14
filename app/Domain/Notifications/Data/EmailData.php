<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Data;

use App\Domain\Notifications\Contracts\NotificationChannelData;
use Spatie\LaravelData\Data;

final class EmailData extends Data implements NotificationChannelData
{
    public function __construct(
        /** @var string|array<int, string> */
        public string|array $to,
        public string $subject,
        public string $template,
        /** @var array<string, mixed> */
        public array $data = [],
        /** @var array<int, string>|null */
        public ?array $cc = null,
        /** @var array<int, string>|null */
        public ?array $bcc = null,
        /** @var array<int, string>|null */
        public ?array $attachments = null,
        public ?string $icon = null,
        public ?string $color = null,
    ) {}
}
