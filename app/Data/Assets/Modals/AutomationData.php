<?php

declare(strict_types=1);

namespace App\Data\Assets\Modals;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;

final class AutomationData extends Data
{
    public function __construct(
        public string $activity,
        public string $frequency,
        #[Date]
        public ?string $last_performed_at = null,
    ) {}
}
