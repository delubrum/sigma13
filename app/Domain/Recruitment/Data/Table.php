<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapInputName('created_at')]
        public readonly string $date,
        #[MapInputName('user_name')]
        public readonly string $user,
        #[MapInputName('approver_name')]
        public readonly string $approver,
        #[MapInputName('assignee_name')]
        public readonly string $assignee,
        #[MapInputName('profile_name')]
        public readonly string $profile,
        #[MapInputName('division_name')]
        public readonly string $division,
        #[MapInputName('area_name')]
        public readonly string $area,
        public readonly string $qty,
        public readonly float $conversion,
        #[MapInputName('days_remaining')]
        public readonly int $days,
        public readonly string $status,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Solicitado', field: 'date', width: 110),
            Column::make(title: 'Usuario', field: 'user', width: 140, headerFilter: 'input'),
            Column::make(title: 'Aprobador', field: 'approver', width: 140),
            Column::make(title: 'Asignado', field: 'assignee', width: 140),
            Column::make(title: 'Perfil', field: 'profile', width: 140, headerFilter: 'input'),
            Column::make(title: 'Vacantes', field: 'qty', width: 100),
            Column::make(title: 'Eficiencia', field: 'conversion', width: 100, hozAlign: 'center'),
            Column::make(title: 'Días', field: 'days', width: 70, hozAlign: 'center'),
            Column::make(title: 'Estado', field: 'status', width: 120, hozAlign: 'center'),
        ];
    }
}
