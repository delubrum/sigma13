{{-- Tab: Info correctivo (fiel al legacy maintenance/info.php) --}}
@php
    /** @var \App\Models\Mnt $ticket */
    /** @var \Illuminate\Database\Eloquent\Collection $assets */
    /** @var \Illuminate\Database\Eloquent\Collection $technicians */
@endphp

<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex justify-center">
        @php
            $statusStyle = match($ticket->status) {
                'Open'     => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'Started'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'Attended' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'Closed','Rated' => 'color:var(--tx2); border-color:var(--b); background:var(--bg2)',
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
        <x-sidebar-row label="Usuario"  :value="$ticket->user?->name ?? '—'" />
        <x-sidebar-row label="Fecha"    :value="$ticket->created_at?->format('Y-m-d H:i') ?? '—'" />
        <x-sidebar-row label="Sede"     :value="$ticket->facility ?? '—'" />
        <x-sidebar-row label="Inicio"   :value="$ticket->started_at?->format('Y-m-d H:i') ?? '—'" />
        <x-sidebar-row label="Cierre"   :value="$ticket->closed_at?->format('Y-m-d H:i') ?? '—'" />
        @if($ticket->rating)
        <x-sidebar-row label="Rating"   :value="$ticket->rating . ' / 5'" />
        @endif
    </x-sidebar-section>

    {{-- Description --}}
    <x-sidebar-section icon="ri-file-text-line" label="Descripción">
        <p class="text-xs leading-relaxed whitespace-pre-line" style="color:var(--tx)">{{ $ticket->description }}</p>
    </x-sidebar-section>

    {{-- Asset (inline editable) --}}
    <x-sidebar-section icon="ri-settings-line" label="Máquina">
        <select data-widget="slimselect"
                hx-post="{{ route('maintenance.update', $id) }}"
                hx-trigger="change"
                hx-vals='{"field": "asset_id"}'
                name="value">
            <option value="">— Sin asignar —</option>
            @foreach($assets as $a)
                <option value="{{ $a->id }}" @selected($a->id === $ticket->asset_id)>
                    {{ mb_convert_case($a->hostname ?? '', MB_CASE_TITLE, 'UTF-8') }} | {{ $a->serial }} | {{ $a->sap }}
                </option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Priority (inline editable) --}}
    <x-sidebar-section icon="ri-flag-line" label="Prioridad">
        <select data-widget="slimselect"
                hx-post="{{ route('maintenance.update', $id) }}"
                hx-trigger="change"
                hx-vals='{"field": "priority"}'
                name="value">
            <option value="High"   @selected($ticket->priority === 'High')>High</option>
            <option value="Medium" @selected($ticket->priority === 'Medium')>Medium</option>
            <option value="Low"    @selected($ticket->priority === 'Low')>Low</option>
        </select>
    </x-sidebar-section>

    {{-- SGC (inline editable) --}}
    <x-sidebar-section icon="ri-settings-3-line" label="SGC">
        <select data-widget="slimselect"
                hx-post="{{ route('maintenance.update', $id) }}"
                hx-trigger="change"
                hx-vals='{"field": "sgc"}'
                name="value">
            <option value="">— Sin categoría —</option>
            @foreach(['Corrective','Preventive','Production','Infrastructure'] as $opt)
                <option value="{{ $opt }}" @selected($ticket->sgc === $opt)>{{ $opt }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Root Cause (inline editable) — exclusivo de Maintenance --}}
    <x-sidebar-section icon="ri-error-warning-line" label="Causa Raíz">
        <select data-widget="slimselect"
                hx-post="{{ route('maintenance.update', $id) }}"
                hx-trigger="change"
                hx-vals='{"field": "root_cause"}'
                name="value">
            <option value="">— Sin causa —</option>
            @foreach(['N/A','Habituales','Falla Eléctrica','Falta Capacitación','Desgaste','Mal Uso'] as $opt)
                <option value="{{ $opt }}" @selected($ticket->root_cause === $opt)>{{ $opt }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Assignee (inline editable) --}}
    <x-sidebar-section icon="ri-shield-user-line" label="Asignado a">
        <select data-widget="slimselect"
                hx-post="{{ route('maintenance.update', $id) }}"
                hx-trigger="change"
                hx-vals='{"field": "assignee_id"}'
                name="value">
            <option value="">— Sin asignar —</option>
            @foreach($technicians as $t)
                <option value="{{ $t->id }}" @selected($t->id === $ticket->assignee_id)>
                    {{ $t->name }}
                </option>
            @endforeach
        </select>
    </x-sidebar-section>

</div>
