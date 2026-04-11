<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Column;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class MovementTable extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $asset_id,
        public readonly bool $is_latest,

        #[Column(
            title: 'Tipo', 
            width: 130, 
            hozAlign: 'center', 
            formatter: 'html', 
            headerFilter: 'list', 
            headerFilterParams: [
                'values' => [
                    'assignment' => 'Asignación',
                    'return' => 'Devolución',
                ]
            ]
        )]
        public readonly string $kind,

        #[Column(title: 'Fecha', width: 120)]
        public readonly string $date,

        #[Column(title: 'Responsable')]
        public readonly string $assignee,

        #[Column(title: 'Hardware')]
        public readonly string $hardware,

        #[Column(title: 'Software')]
        public readonly string $software,

        #[Column(title: 'Acta/Notas', width: 100, hozAlign: 'center', formatter: 'html')]
        public readonly string $minute,

        #[Column(title: 'Acciones', width: 60, hozAlign: 'center', formatter: 'html')]
        public readonly string $actions,
    ) {}

    public static function fromModel(AssetEvent $event, bool $isLatest = false): self
    {
        $date = $event->created_at instanceof Carbon
            ? $event->created_at->format('d/m/Y H:i')
            : '---';

        // Formateo de Hardware
        $hardware = is_array($event->hardware) 
            ? collect($event->hardware)->filter()->map(fn($v, $k) => is_numeric($k) ? $v : "$k: $v")->implode(', ')
            : ($event->hardware ?: '---');

        // Formateo de Software
        $software = is_array($event->software)
            ? collect($event->software)->filter()->map(fn($v, $k) => is_numeric($k) ? $v : "$k: $v")->implode(', ')
            : ($event->software ?: '---');

        // Lógica de Actas (Minutas) - Buscamos en Media Library y en Legado
        $minuteUrl = null;
        
        // 1. Intentar Media Library (Nuevo sistema)
        $media = $event->getFirstMedia('minute');
        $minuteUrl = $media ? route('shared.media.download', $media->id) : null;

        // 2. Intentar Legado (Ruta física)
        if (!$minuteUrl) {
            $legacyPath = "uploads/assets/{$event->asset_id}/{$event->kind}/{$event->id}.pdf";
            if (file_exists(public_path($legacyPath))) {
                $minuteUrl = asset($legacyPath) . '?t=' . time();
            }
        }

        $minuteHtml = $minuteUrl 
            ? '<a href="'.$minuteUrl.'" target="_blank" class="text-indigo-600 font-bold hover:scale-110 inline-block transition-transform"><i class="ri-file-pdf-2-line text-lg"></i></a>'
            : '<span class="opacity-20">---</span>';

        $kindHtml = $event->kind === 'assignment'
            ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/10 text-blue-600 border border-blue-500/20 w-full block text-center"><i class="ri-arrow-right-line mr-1"></i>Asignación</span>'
            : '<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 w-full block text-center"><i class="ri-arrow-left-line mr-1"></i>Devolución</span>';

        $actions = '';
        if (auth()->user()?->can('140')) {
            // Solo se permite editar si es la última asignación activa
            if ($isLatest && $event->kind === 'assignment') {
                $actions = '<button hx-get="'.route('assets.assignments.edit', $event->id).'" 
                                    hx-target="#modal-body-2"
                                    hx-indicator="#global-loader"
                                    hx-on::after-request="if(event.detail.successful) window.dispatchEvent(new CustomEvent(\'open-modal-2\'))"
                                    title="Editar Asignación"
                                    class="text-blue-500 hover:scale-125 transition-transform inline-block px-2"><i class="ri-pencil-line text-lg"></i></button>';
            }
        }

        return new self(
            id: $event->id,
            asset_id: $event->asset_id,
            is_latest: $isLatest,
            kind: $kindHtml,
            date: $date,
            assignee: $event->employee->name ?? '---',
            hardware: $hardware,
            software: $software,
            minute: $minuteHtml,
            actions: $actions,
        );
    }
}
