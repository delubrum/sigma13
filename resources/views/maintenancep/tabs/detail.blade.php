{{-- Tab: Info del preventivo (fiel al legacy maintenancep/info.php) --}}
@php
    /** @var \App\Models\MntPreventive $ticket */
@endphp

<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex justify-center">
        @php
            $statusStyle = match($ticket->status) {
                'Open'     => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'Started'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'Attended' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'Closed'   => 'color:var(--tx2); border-color:var(--b); background:var(--bg2)',
                'Rejected' => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
                default    => '',
            };
        @endphp
        <span class="px-4 py-1.5 rounded-full text-sm font-bold border-2 shadow-sm" style="{{ $statusStyle }}">
            {{ $ticket->status }}
        </span>
    </div>

    {{-- Basic Information --}}
    <x-sidebar-section icon="ri-information-line" label="Información Básica">
        <x-sidebar-row label="ID"        :value="$ticket->id" />
        <x-sidebar-row label="Fecha Gen" :value="$ticket->created_at?->format('Y-m-d H:i') ?? '—'" />
        <x-sidebar-row label="Frecuencia" :value="$ticket->frequency ?? '—'" />
        <x-sidebar-row label="Equipo"     :value="$ticket->asset?->hostname . ' | ' . $ticket->asset?->serial" />
        <x-sidebar-row label="Inicio Real" :value="$ticket->started ?? '—'" />
        <x-sidebar-row label="Fin Real"    :value="$ticket->closed_at?->format('Y-m-d H:i') ?? '—'" />
    </x-sidebar-section>

    {{-- Programming (Editable with Flatpickr) --}}
    <x-sidebar-section icon="ri-calendar-event-line" label="Programación">
        <div class="space-y-2">
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Inicio Programado</label>
                <input type="text" data-widget="flatpickr" 
                       value="{{ $ticket->scheduled_start }}"
                       hx-post="{{ route('maintenancep.update', $id) }}"
                       hx-trigger="change"
                       hx-vals='{"field": "scheduled_start"}'
                       name="value"
                       class="sigma-input text-xs w-full">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Vencimiento</label>
                <input type="text" data-widget="flatpickr" 
                       value="{{ $ticket->scheduled_end }}"
                       hx-post="{{ route('maintenancep.update', $id) }}"
                       hx-trigger="change"
                       hx-vals='{"field": "scheduled_end"}'
                       name="value"
                       class="sigma-input text-xs w-full">
            </div>
        </div>
    </x-sidebar-section>

    {{-- Activity / Description --}}
    <x-sidebar-section icon="ri-list-check" label="Actividad">
        <p class="text-xs leading-relaxed whitespace-pre-line" style="color:var(--tx)">{{ $ticket->activity }}</p>
    </x-sidebar-section>

</div>
