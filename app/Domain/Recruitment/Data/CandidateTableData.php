<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class CandidateTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Tipo', width: 100, headerFilter: 'input')]
        public ?string $kind,

        #[Column(title: 'Nombre', width: 180, headerFilter: 'input')]
        public ?string $name,

        #[Column(title: 'CC', width: 110, hozAlign: 'center', headerFilter: 'input')]
        public ?string $cc,

        #[Column(title: 'Email', width: 200, headerFilter: 'input')]
        public ?string $email,

        #[Column(title: 'Teléfono', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public ?string $phone,

        #[Column(title: 'Cita', width: 140, hozAlign: 'center', headerFilter: 'input')]
        public ?string $appointment,

        #[Column(title: 'Reclutador', width: 130, headerFilter: 'input')]
        public ?string $recruiter,

        #[Column(
            title: 'Estado',
            width: 110,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'appointment' => 'Cita',
                    'screening'   => 'Entrevista',
                    'hired'       => 'Contratado',
                    'active'      => 'Activo',
                    'qualified'   => 'Calificado',
                    'list'        => 'Lista',
                    'discarded'   => 'Descartado',
                    'polygraph'   => 'Polígrafía',
                    'security'    => 'Seguridad',
                    'medical'     => 'Médico',
                    'home'        => 'Domicilio',
                ],
                'clearable' => true,
            ]
        )]
        public string $status,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $status = (string) ($row->status ?? 'appointment');
        $color  = match ($status) {
            'hired', 'active' => 'border-green-500 text-green-500',
            'screening'       => 'border-blue-500 text-blue-500',
            'appointment'     => 'border-yellow-500 text-yellow-500',
            'qualified'       => 'border-purple-500 text-purple-500',
            'discarded'       => 'border-red-500 text-red-500',
            default           => 'border-sigma-b text-sigma-tx2',
        };

        return new self(
            id:          (int) ($row->id ?? 0),
            kind:        isset($row->kind)        ? (string) $row->kind        : null,
            name:        isset($row->name)        ? (string) $row->name        : null,
            cc:          isset($row->cc)          ? (string) $row->cc          : null,
            email:       isset($row->email)       ? (string) $row->email       : null,
            phone:       isset($row->phone)       ? (string) $row->phone       : null,
            appointment: isset($row->appointment) ? (string) $row->appointment : null,
            recruiter:   isset($row->recruiter_name) ? (string) $row->recruiter_name : null,
            status:      "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>",
        );
    }
}
