<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\Shared\Data\Tab;
use Spatie\LaravelData\Data;

final class TabsData extends Data
{
    public function __construct(
        #[Tab(
            label:   'Tareas y Tiempos',
            icon:    'ri-time-line',
            route:   'helpdesk.tasks',
            default: true,
        )]
        public string $tasks = '',
    ) {}
}
