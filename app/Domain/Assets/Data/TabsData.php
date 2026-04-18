<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Shared\Data\Tab;
use Spatie\LaravelData\Data;

final class TabsData extends Data
{
    public function __construct(
        #[Tab(label: 'Detalles',    icon: 'ri-information-line',      route: 'assets.details',     default: true)]
        public string $details = '',

        #[Tab(label: 'Movimientos', icon: 'ri-arrow-left-right-line',  route: 'assets.movements')]
        public string $movements = '',

        #[Tab(label: 'Documentos',  icon: 'ri-file-line',              route: 'assets.documents')]
        public string $documents = '',

        #[Tab(label: 'Automations', icon: 'ri-settings-4-line',        route: 'assets.automations')]
        public string $automations = '',

        #[Tab(label: 'Correctivos', icon: 'ri-tools-line',             route: 'assets.maintenances')]
        public string $maintenances = '',

        #[Tab(label: 'Preventivos', icon: 'ri-calendar-check-line',    route: 'assets.preventive')]
        public string $preventive = '',

        #[Tab(label: 'SIGMA AI',    icon: 'ri-robot-2-line',           route: 'assets.ai')]
        public string $ai = '',
    ) {}
}
