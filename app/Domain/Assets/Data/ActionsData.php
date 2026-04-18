<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Shared\Data\Action;
use Spatie\LaravelData\Data;

final class ActionsData extends Data
{
    public function __construct(
        #[Action(
            label:  'Editar Activo',
            icon:   'ri-edit-line',
            route:  'assets/create',
            target: '#modal-body',
            level:  1,
        )]
        public string $edit = '',

        #[Action(
            label:  'Dar de Baja',
            icon:   'ri-delete-bin-line',
            route:  'assets/dispose',
            target: '#modal-body-2',
            level:  2,
        )]
        public string $dispose = '',
    ) {}
}
