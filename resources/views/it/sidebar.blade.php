{{-- Sidebar del ticket — el ID ya aparece en el header del modal --}}
@php
    /** @var \App\Data\IT\Sidebar|\App\Data\Maintenance\Sidebar $data */
    $statusStyle = match($data->status) {
        'Open'     => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
        'Started'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
        'Attended' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
        'Closed',
        'Rated'    => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
        'Rejected' => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
        default    => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
    };
@endphp

<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex items-center justify-between gap-2">
        <span class="px-3 py-1 rounded-full text-xs font-bold border" style="{{ $statusStyle }}">
            {{ $data->status }}
        </span>
        @if(!empty($data->priority))
        <span class="text-[11px] font-bold uppercase tracking-widest" style="color:var(--tx2)">
            {{ $data->priority }}
        </span>
        @endif
    </div>

    {{-- Personas --}}
    <x-sidebar-section icon="ri-user-line" label="Personas">
        <x-sidebar-row label="Solicitado por" :value="$data->user ?? '—'" id="sidebarUser" />
        <x-sidebar-row label="Asignado a"     :value="$data->assignee ?? '—'" id="sidebarAssignee" />
    </x-sidebar-section>

    {{-- Sede --}}
    @if(!empty($data->facility))
    <x-sidebar-section icon="ri-map-pin-line" label="Sede">
        <x-sidebar-row label="Instalación" :value="$data->facility" />
    </x-sidebar-section>
    @endif

    {{-- Tiempos --}}
    <x-sidebar-section icon="ri-time-line" label="Tiempos">
        <x-sidebar-row label="Creado"  :value="$data->createdAt ?? '—'" />
        <x-sidebar-row label="Inicio"  :value="$data->startedAt ?? '—'" />
        <x-sidebar-row label="Cierre"  :value="$data->closedAt  ?? '—'" />
    </x-sidebar-section>

    {{-- Cierre --}}
    @if(property_exists($data,'sgc') && $data->sgc)
    <x-sidebar-section icon="ri-shield-check-line" label="Cierre">
        <x-sidebar-row label="SGC"    :value="$data->sgc" />
        @if(property_exists($data,'rating') && $data->rating)
        <x-sidebar-row label="Rating" :value="$data->rating . ' / 5'" />
        @endif
    </x-sidebar-section>
    @endif

</div>
