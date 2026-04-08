{{-- Sidebar de Preventivo (MaintenanceP) --}}
@php
    /** @var \App\Data\MaintenanceP\Sidebar $data */
    $statusStyle = match($data->status) {
        'Open'     => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
        'Started'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
        'Attended' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
        'Closed'   => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
        'Rejected' => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
        default    => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
    };
@endphp

<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex items-center justify-center">
        <span class="px-4 py-1 rounded-full text-xs font-bold border-2 shadow-sm" style="{{ $statusStyle }}">
            {{ $data->status }}
        </span>
    </div>

    {{-- Asset --}}
    <x-sidebar-section icon="ri-settings-line" label="Activo">
        <x-sidebar-row label="Equipo" :value="$data->asset ?? '—'" />
    </x-sidebar-section>

    {{-- Programming --}}
    <x-sidebar-section icon="ri-calendar-todo-line" label="Programación">
        <x-sidebar-row label="Frecuencia" :value="$data->frequency ?? '—'" />
        <x-sidebar-row label="Inicio Prog." :value="$data->scheduledStart ?? '—'" />
        <x-sidebar-row label="Vencimiento"  :value="$data->scheduledEnd   ?? '—'" />
    </x-sidebar-section>

    {{-- Real Times --}}
    <x-sidebar-section icon="ri-time-line" label="Tiempos Reales">
        <x-sidebar-row label="Inicio"   :value="$data->started  ?? '—'" />
        <x-sidebar-row label="Atención" :value="$data->attended ?? '—'" />
        <x-sidebar-row label="Cierre"   :value="$data->closedAt ?? '—'" />
    </x-sidebar-section>

</div>
