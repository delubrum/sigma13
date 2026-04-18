<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(
            label: 'Tipo',
            type: 'select',
            options: [
                'IT'       => 'IT',
                'Locative' => 'Locative',
                'Machinery'=> 'Machinery',
                'OHS'      => 'OHS',
            ],
            widget: 'slimselect',
            width: FieldWidth::Full,
        )]
        #[Required]
        public readonly string $kind,

        #[Field(
            label: 'Sede',
            type: 'select',
            options: [
                'ESM1'     => 'ESM1',
                'ESM2'     => 'ESM2',
                'ESM3'     => 'ESM3',
                'Medellín' => 'Medellín',
            ],
            widget: 'slimselect',
            width: FieldWidth::Full,
        )]
        #[Required, Max(100)]
        public readonly string $facility,

        #[Field(
            label: 'Activo',
            type: 'select',
            placeholder: 'Seleccionar activo...',
            widget: 'slimselect',
            width: FieldWidth::Full,
            route: 'global.options',
            routeParams: ['model' => 'assets', 'area' => 'Machinery,Vehicles,Locative'],
            dependsOn: 'kind',
            showWhen: ['Machinery', 'Locative'],
        )]
        public readonly ?int $asset_id,

        #[Field(
            label: 'Prioridad',
            type: 'select',
            options: [
                'High'   => 'Right Now — Bloqueado',
                'Medium' => 'Today — Necesita Atención',
                'Low'    => 'Tomorrow — Puede Esperar',
            ],
            widget: 'slimselect',
            width: FieldWidth::Full,
        )]
        #[Required, Max(50)]
        public readonly string $priority,

        #[Field(
            label: 'Descripción',
            type: 'textarea',
            placeholder: 'Describe el problema en detalle...',
            rows: 4,
            width: FieldWidth::Full,
        )]
        #[Required]
        public readonly string $description,

        #[Field(label: 'Foto (opcional)', type: 'file', widget: 'sigma-file', accept: 'image/*', width: FieldWidth::Full)]
        public readonly mixed $files = null,
    ) {}
}
