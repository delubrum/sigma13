<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use App\Domain\Shared\Data\Field;

final class AssignmentModalData extends Data
{
    public function __construct(
        #[Required, Exists('employees', 'id')]
        #[Field(label: 'Responsable', widget: 'slimselect')]
        public int $employee_id,

        /** @var array<int, string> */
        #[ArrayType]
        #[Field(label: 'Hardware', widget: 'hardware-list')]
        public array $hardware = [],

        /** @var array<int, string> */
        #[ArrayType]
        #[Field(label: 'Software', widget: 'software-list')]
        public array $software = [],

        #[Field(label: 'Observaciones')]
        public ?string $notes = null,

        public mixed $file = null,
    ) {}
}