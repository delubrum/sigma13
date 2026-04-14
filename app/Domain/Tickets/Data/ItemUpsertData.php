<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Data;

final class ItemUpsertData extends Data
{
    public function __construct(
        public readonly int $id, // ID del Ticket padre

        #[Field(label: 'Atiende', type: 'select', options: ['Internal' => 'Internal', 'External' => 'External'])]
        public readonly string $attends,

        #[Field(label: 'Notas/Actividad', type: 'textarea', placeholder: 'Describe el trabajo realizado...')]
        public readonly string $notes,
    ) {}
}
