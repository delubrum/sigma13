<div class="p-4 space-y-4 scrollbar-thin overflow-y-auto max-h-[85vh]">

    {{-- Status Badge --}}
    <div class="flex justify-center flex-col items-center mb-2">
        @php
            $statusLabel = match($data->status) {
                'Analysis' => 'En Análisis',
                'Plan'     => 'En Plan',
                'Closure'  => 'En Cierre',
                'Closed'   => 'Cerrado',
                'Rejected' => 'Rechazado',
                'Canceled' => 'Cancelado',
                default    => $data->status,
            };
            $statusStyle = match($data->status) {
                'Analysis' => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'Plan'     => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'Closure'  => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'Closed'   => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                default    => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
            };
        @endphp
        <span class="px-4 py-1.5 rounded-full text-sm font-bold border-2 shadow-md italic"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    @if($data->rejectionReason)
        <div class="px-3 py-2 rounded-lg text-[11px] font-medium" style="background:var(--warning-bg); color:var(--warning); border:1px solid var(--warning-muted)">
            <i class="ri-information-line mr-1"></i> {{ $data->rejectionReason }}
        </div>
    @endif

    {{-- Basic Info --}}
    <x-sidebar-section icon="ri-information-line" label="Información Básica">
        <x-sidebar-row label="Código"      :value="$data->code" />
        <x-sidebar-row label="Creador"     :value="$data->creator" />
        <x-sidebar-row label="Fecha"       :value="$data->createdAt" />
        <x-sidebar-row label="Proceso"     :value="$data->process" />
        <x-sidebar-row label="Perspectiva" :value="$data->perspective" />
        <x-sidebar-row label="Tipo"        :value="$data->type" />
        <x-sidebar-row label="Fuente"      :value="$data->source . ($data->sourceOther ? ' — '.$data->sourceOther : '')" />
        <x-sidebar-row label="Repetida"    :value="$data->repeated" />
        <x-sidebar-row label="Responsable" :value="$data->responsible" />
    </x-sidebar-section>

    {{-- Description --}}
    <x-sidebar-section icon="ri-file-text-line" label="Descripción">
        <div class="text-[11px] leading-relaxed opacity-90 p-3 rounded-xl border italic"
             style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            {{ $data->description }}
        </div>
    </x-sidebar-section>

    @if($data->immediateAction)
        <x-sidebar-section icon="ri-flashlight-line" label="Acción Inmediata">
            <div class="text-[11px] leading-relaxed opacity-90 p-3 rounded-xl border italic"
                 style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
                {{ $data->immediateAction }}
            </div>
        </x-sidebar-section>
    @endif

    {{-- Aim & Goal (editable) --}}
    <x-sidebar-section icon="ri-target-line" label="Objetivo">
        @if($data->canEdit)
            <label class="text-[10px] font-black uppercase opacity-50 block mb-1">Objetivo del Plan</label>
            <textarea
                name="aim"
                rows="3"
                class="w-full text-[11px] rounded-lg border p-2 resize-none"
                style="background:var(--bg2); border-color:var(--b); color:var(--tx)"
                hx-post="{{ route('improvement.patch', $data->id) }}"
                hx-vals='{"field":"aim"}'
                hx-trigger="blur"
                hx-swap="none">{{ $data->aim }}</textarea>
            <label class="text-[10px] font-black uppercase opacity-50 block mb-1 mt-2">Meta</label>
            <textarea
                name="goal"
                rows="3"
                class="w-full text-[11px] rounded-lg border p-2 resize-none"
                style="background:var(--bg2); border-color:var(--b); color:var(--tx)"
                hx-post="{{ route('improvement.patch', $data->id) }}"
                hx-vals='{"field":"goal"}'
                hx-trigger="blur"
                hx-swap="none">{{ $data->goal }}</textarea>
        @else
            <x-sidebar-row label="Objetivo" :value="$data->aim" />
            <x-sidebar-row label="Meta"     :value="$data->goal" />
        @endif
    </x-sidebar-section>

    {{-- Involved Users (editable) --}}
    <x-sidebar-section icon="ri-group-line" label="Personas Involucradas">
        @if($data->canEdit)
            @php
                $userOptions = collect($data->allUsers)->pluck('name', 'id')->toArray();
            @endphp
            <select
                name="user_ids[]"
                multiple
                data-widget="slimselect"
                class="w-full"
                hx-post="{{ route('improvement.patch', $data->id) }}"
                hx-vals='{"field":"user_ids"}'
                hx-trigger="change"
                hx-swap="none">
                @foreach($data->allUsers as $u)
                    <option value="{{ $u['id'] }}" {{ in_array($u['id'], $data->userIds) ? 'selected' : '' }}>
                        {{ $u['name'] }}
                    </option>
                @endforeach
            </select>
        @else
            @php
                $names = collect($data->allUsers)->whereIn('id', $data->userIds)->pluck('name')->implode(', ');
            @endphp
            <div class="text-[11px] font-bold">{{ $names ?: '—' }}</div>
        @endif
    </x-sidebar-section>

    {{-- Closure Details --}}
    @if($data->closedAt)
        <x-sidebar-section icon="ri-checkbox-circle-line" label="Cierre">
            <x-sidebar-row label="Fecha cierre"  :value="$data->cdate" />
            <x-sidebar-row label="Conveniencia"  :value="$data->convenience" />
            <x-sidebar-row label="Adecuación"    :value="$data->adequacy" />
            <x-sidebar-row label="Eficacia"      :value="$data->effectiveness" />
            @if($data->notes)
                <x-sidebar-row label="Notas" :value="$data->notes" />
            @endif
        </x-sidebar-section>
    @endif

</div>
