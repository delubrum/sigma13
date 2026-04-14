<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Field;
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
                'Barranquilla' => 'Barranquilla',
            ]
        )]
        public readonly string $facility,

        #[Field(
            label: 'Type',
            type: 'select',
            options: [
                'HR' => 'HR',
                'OHS' => 'OHS',
                'Marketing' => 'Marketing',
            ]
        )]
        public readonly string $kind,

        #[Field(
            label: 'Priority',
            type: 'select',
            options: [
                'High' => 'Right Now. Locked',
                'Medium' => 'Today. Need Attention',
                'Low' => 'Tomorrow. I Can Wait',
            ]
        )]
        public readonly string $priority,

        #[Field(
            label: 'Description',
            type: 'textarea',
            placeholder: 'Describe the issue in detail...'
        )]
        public readonly string $description,
    ) {}
}
