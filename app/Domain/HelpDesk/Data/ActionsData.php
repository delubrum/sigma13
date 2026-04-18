<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\Shared\Data\Action;
use Spatie\LaravelData\Data;

final class ActionsData extends Data
{
    public function __construct(
        #[Action(
            label:       'Atender',
            icon:        'ri-play-line',
            route:       'helpdesk/action/attend',
            method:      'POST',
            confirm:     '¿Atender este ticket?',
            showWhenCan: ['canClose'],
        )]
        public string $attend = '',

        #[Action(
            label:       'Cerrar',
            icon:        'ri-checkbox-circle-line',
            route:       'helpdesk/action/close',
            method:      'POST',
            confirm:     '¿Cerrar este ticket?',
            showWhenCan: ['canClose'],
        )]
        public string $close = '',

        #[Action(
            label:       'Rechazar',
            icon:        'ri-close-circle-line',
            route:       'helpdesk/action/reject',
            method:      'POST',
            prompt:      '¿Motivo del rechazo?',
            showWhenCan: ['canClose'],
        )]
        public string $reject = '',
    ) {}
}
