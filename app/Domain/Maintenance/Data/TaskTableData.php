<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TaskTableData extends Data
{
    public function __construct(
        #[Column(title: 'Date', width: 150)]
        public string $date,

        #[Column(title: 'Operator', width: 120)]
        public string $user,

        #[Column(title: 'Complexity', width: 100)]
        public ?string $complexity,

        #[Column(title: 'Attends', width: 100)]
        public ?string $attends,

        #[Column(title: 'Time (m)', width: 80, hozAlign: 'center')]
        public float $duration,

        #[Column(title: 'Notes', width: 300)]
        public ?string $notes,

        #[Column(title: 'File', width: 60, hozAlign: 'center', formatter: 'html')]
        public ?string $file = null,
    ) {}
}
