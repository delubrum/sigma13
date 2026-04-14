<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TaskTableData extends Data
{
    public function __construct(
        #[Column(title: 'Fecha', field: 'date', width: 140, hozAlign: 'center')]
        public string $date,

        #[Column(title: 'Operador', field: 'user', width: 150)]
        public string $user,

        #[Column(title: 'Atiende', field: 'attends', width: 100, hozAlign: 'center')]
        public string $attends,

        #[Column(title: 'Notas', field: 'notes', formatter: 'textarea')]
        public string $notes,

        #[Column(title: 'Archivo', field: 'file', width: 100, hozAlign: 'center', formatter: 'html')]
        public ?string $file,
    ) {}
}
