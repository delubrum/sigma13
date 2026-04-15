<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(
            label: 'Facility',
            type: 'select',
            options: [
                'ESM1' => 'ESM1',
                'ESM2' => 'ESM2',
                'ESM3' => 'ESM3',
                'Medellín' => 'Medellín',
            ],
            width: FieldWidth::Half
        )]
        public readonly string $facility,

        #[Field(
            label: 'Machine / Asset',
            type: 'select',
            placeholder: 'Select...',
            widget: 'slimselect',
            width: FieldWidth::Half,
            route: 'global.options',
            routeParams: ['model' => 'assets']
        )]
        public readonly int $asset_id,

        #[Field(
            label: 'Priority',
            type: 'select',
            options: [
                'High' => 'Right Now. Locked',
                'Medium' => 'Today. Need Attention',
                'Low' => 'Tomorrow. I Can Wait',
            ],
            width: FieldWidth::Full
        )]
        public readonly string $priority,

        #[Field(
            label: 'Description',
            type: 'textarea',
            placeholder: 'Describe the issue in detail...',
            width: FieldWidth::Full
        )]
        public readonly string $description,

        #[Field(
            label: 'Picture',
            type: 'file',
            widget: 'sigma-file',
            width: FieldWidth::Full,
            multiple: true
        )]
        public readonly ?array $files = null,
    ) {}
}
