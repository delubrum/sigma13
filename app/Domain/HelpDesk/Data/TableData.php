<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\HelpDesk\Models\Issue;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: '#', width: 65, hozAlign: 'center', frozen: true)]
        public readonly int $id,

        #[Column(title: 'Fecha', width: 130, hozAlign: 'center')]
        public readonly string $created_at,

        #[Column(
            title: 'Tipo',
            width: 110,
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'IT'        => 'IT',
                    'Locative'  => 'Locative',
                    'Machinery' => 'Machinery',
                    'OHS'       => 'OHS',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $kind,

        #[Column(
            title: 'Sede',
            width: 100,
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'ESM1'     => 'ESM1',
                    'ESM2'     => 'ESM2',
                    'Medellín' => 'Medellín',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $facility,

        #[Column(title: 'Activo', width: 200, headerFilter: 'input')]
        public readonly string $asset,

        #[Column(
            title: 'Prioridad',
            width: 100,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'High'   => 'Alta',
                    'Medium' => 'Media',
                    'Low'    => 'Baja',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $priority,

        #[Column(
            title: 'Estado',
            width: 110,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'Open'     => 'Abierto',
                    'Started'  => 'Iniciado',
                    'Attended' => 'Atendido',
                    'Closed'   => 'Cerrado',
                    'Rated'    => 'Calificado',
                    'Rejected' => 'Rechazado',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Descripción', width: 280, headerFilter: 'input')]
        public readonly string $description,

        #[Column(title: 'Reportó', width: 140, headerFilter: 'input')]
        public readonly string $reporter,

        #[Column(title: 'Asignado', width: 140, headerFilter: 'input')]
        public readonly string $assignee,

        #[Column(title: 'Días', width: 70, hozAlign: 'center')]
        public readonly int $days,

        #[Column(title: 'Horas', width: 80, hozAlign: 'center')]
        public readonly float $hours,

        #[Column(title: 'F. Inicio', width: 120, hozAlign: 'center')]
        public readonly string $started_at,

        #[Column(title: 'F. Atendido', width: 120, hozAlign: 'center')]
        public readonly string $ended_at,

        #[Column(title: 'F. Cierre', width: 120, hozAlign: 'center')]
        public readonly string $closed_at,

        #[Column(title: 'Complejidad', width: 120)]
        public readonly string $complexity,

        #[Column(title: 'SGC', width: 110)]
        public readonly string $sgc_code,

        #[Column(title: 'Causa Raíz', width: 160)]
        public readonly string $root_cause,

        #[Column(title: 'Rating', width: 80, hozAlign: 'center')]
        public readonly int $rating,
    ) {}

    public static function fromModel(mixed $issue): self
    {
        /** @var Issue $issue */
        $created  = $issue->created_at;
        $closed   = $issue->closed_at ?? now();
        $days     = $created ? (int) $created->diffInDays($closed) : 0;
        $hours    = round((float) ($issue->time_sum ?? 0) / 60, 1);

        return new self(
            id:          $issue->id,
            created_at:  $created?->format('d/m/Y H:i') ?? '',
            kind:        $issue->kind,
            facility:    $issue->facility,
            asset:       self::formatAsset($issue),
            priority:    self::badgePriority($issue->priority),
            status:      self::badgeStatus($issue->status ?? 'Open'),
            description: $issue->description,
            reporter:    $issue->reporter?->name ?? '—',
            assignee:    $issue->assignee?->name ?? '—',
            days:        $days,
            hours:       $hours,
            started_at:  $issue->started_at?->format('d/m/Y') ?? '—',
            ended_at:    $issue->ended_at?->format('d/m/Y') ?? '—',
            closed_at:   $issue->closed_at?->format('d/m/Y') ?? '—',
            complexity:  $issue->complexity ?? '—',
            sgc_code:    $issue->sgc_code ?? '—',
            root_cause:  $issue->root_cause ?? '—',
            rating:      $issue->rating ?? 0,
        );
    }

    private static function formatAsset(Issue $issue): string
    {
        $asset = $issue->asset;
        if (! $asset) {
            return '—';
        }
        $parts = array_filter([$asset->hostname, $asset->serial, $asset->sap]);

        return mb_convert_case(implode(' | ', $parts), MB_CASE_TITLE, 'UTF-8');
    }

    private static function badgePriority(?string $priority): string
    {
        if (! $priority) {
            return '—';
        }
        $color = match (strtolower($priority)) {
            'high'   => 'border-red-500 text-red-500',
            'medium' => 'border-orange-500 text-orange-500',
            default  => 'border-gray-400 text-gray-400',
        };
        $label = match (strtolower($priority)) {
            'high'   => 'Alta',
            'medium' => 'Media',
            'low'    => 'Baja',
            default  => $priority,
        };

        return "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$label}</span>";
    }

    private static function badgeStatus(string $status): string
    {
        $color = match (strtolower($status)) {
            'open'     => 'border-gray-400 text-gray-400',
            'started'  => 'border-yellow-500 text-yellow-500',
            'attended' => 'border-purple-500 text-purple-500',
            'closed'   => 'border-blue-500 text-blue-500',
            'rated'    => 'border-green-500 text-green-500',
            'rejected' => 'border-red-500 text-red-500',
            default    => 'border-sigma-b text-sigma-tx2',
        };
        $label = match (strtolower($status)) {
            'open'     => 'Abierto',
            'started'  => 'Iniciado',
            'attended' => 'Atendido',
            'closed'   => 'Cerrado',
            'rated'    => 'Calificado',
            'rejected' => 'Rechazado',
            default    => $status,
        };

        return "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$label}</span>";
    }
}
