<?php

declare(strict_types=1);

namespace App\Domain\Printing\Data;

use App\Domain\Printing\Models\Wo;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'WO', width: 140)]
        public readonly string $id,

        #[Column(title: 'Proyecto', width: 250)]
        public readonly string $project,

        #[Column(title: 'Usuario', width: 150)]
        public readonly string $user,

        #[Column(title: 'ES ID', width: 160)]
        public readonly string $es,

        #[Column(title: 'Fecha', width: 140, hozAlign: 'center')]
        public readonly string $date,
    ) {}

    public static function fromModel(mixed $wo): self
    {
        /** @var Wo $wo */
        return new self(
            id: $wo->code,
            project: $wo->project ?? '',
            user: $wo->username ?? '',
            es: $wo->es_id ?? '',
            date: $wo->created_at?->format('Y-m-d H:i') ?? '',
        );
    }
}
