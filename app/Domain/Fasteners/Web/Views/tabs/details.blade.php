<?php
/** @var object{model: \App\Domain\Fasteners\Models\Fastener} $data */
$model = $data->model;
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-4">
        <h4 class="text-[10px] font-black uppercase tracking-widest opacity-50 border-b pb-2" style="border-color:var(--b)">Información Técnica</h4>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Código Interno</span>
                <span class="text-sm font-bold">{{ $model->code }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Categoría</span>
                <span class="text-sm font-bold">{{ $model->category ?? '—' }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b) col-span-2">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Descripción</span>
                <span class="text-xs font-medium">{{ $model->description ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <h4 class="text-[10px] font-black uppercase tracking-widest opacity-50 border-b pb-2" style="border-color:var(--b)">Dimensiones y Cabeza</h4>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Tipo de Cabeza</span>
                <span class="text-xs font-bold">{{ $model->head ?? '—' }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Destornillador / Llave</span>
                <span class="text-xs font-bold">{{ $model->screwdriver ?? '—' }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Diámetro</span>
                <span class="text-xs font-bold">{{ $model->diameter ?? '—' }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border bg-sigma-bg2" style="border-color:var(--b)">
                <span class="text-[9px] font-black uppercase opacity-60 mb-1">Longitud Nominal</span>
                <span class="text-xs font-bold">{{ $model->item_length ?? '—' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <h4 class="text-[10px] font-black uppercase tracking-widest opacity-50 border-b pb-2 mb-4" style="border-color:var(--b)">Observaciones Adicionales</h4>
    <div class="p-4 rounded-xl border bg-sigma-bg/30 italic text-sm" style="border-color:var(--b)">
        {{ $model->observation ?: 'Sin observaciones registradas.' }}
    </div>
</div>
