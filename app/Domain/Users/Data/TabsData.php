<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Tab;
use Spatie\LaravelData\Data;

final class TabsData extends Data
{
    public function __construct(
        #[Tab(label: 'Permisos y Acceso', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true)]
        public readonly string $info = '',
    ) {}
}
