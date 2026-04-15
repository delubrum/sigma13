<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class TaskUpsertData extends Data
{
    public function __construct(
        public ?int $id,

        #[MapInputName('mnt_id')]
        public int $maintenanceId,

        #[Field(label: 'Complexity', type: 'select', options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'], width: FieldWidth::Half)]
        public string $complexity,

        #[Field(label: 'Attends', type: 'select', options: ['Corrective' => 'Corrective', 'Preventive' => 'Preventive', 'Change' => 'Change'], width: FieldWidth::Half)]
        public string $attends,

        #[Field(label: 'Duration (minutes)', type: 'number', width: FieldWidth::Half)]
        public float $duration,

        #[Field(label: 'Notes', type: 'textarea', width: FieldWidth::Full)]
        public string $notes,

        #[Field(label: 'Picture/File', type: 'file', widget: 'sigma-file', width: FieldWidth::Full)]
        public ?array $files = null,
    ) {}
}
