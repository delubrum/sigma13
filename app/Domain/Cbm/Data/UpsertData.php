<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public ?int $id,

        #[Field(label: 'Project Name', width: FieldWidth::Full, placeholder: 'e.g. Export Dubai 2024', required: true)]
        public string $project,

        #[Field(label: 'Dimensions Excel (XLSX)', type: 'file', width: FieldWidth::Full, required: true)]
        public mixed $excel_file = null,
    ) {}
}
