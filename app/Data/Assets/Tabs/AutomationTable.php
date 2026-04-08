<?php

declare(strict_types=1);

namespace App\Data\Assets\Tabs;

use App\Models\MntPreventiveForm;
use Spatie\LaravelData\Data;

final class AutomationTable extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $activity,
        public readonly string $frequency,
        public readonly string $last_performed_at,
    ) {}

    public static function fromModel(MntPreventiveForm $task): self
    {
        $last = $task->last_performed_at;
        $lastDate = $last instanceof \DateTimeInterface 
            ? $last->format('d/m/Y') 
            : (is_string($last) ? $last : '---');

        return new self(
            id: $task->id,
            activity: $task->activity ?? '---',
            frequency: $task->frequency ?? '---',
            last_performed_at: $lastDate,
        );
    }
}
