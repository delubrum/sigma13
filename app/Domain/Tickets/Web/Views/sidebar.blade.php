@php
    use App\Domain\Shared\Data\Field;
@endphp

<div class="p-4 space-y-4 scrollbar-thin overflow-y-auto max-h-[85vh]">
    {{-- Status Badge --}}
    <div class="flex justify-center flex-col items-center mb-2">
        @php
            $statusLabel = match($data->status) {
                'Open'     => 'Abierto',
                'Started'  => 'En Proceso',
                'Closed'   => 'Cerrado',
                'Rated'    => 'Calificado',
                'Rejected' => 'Rechazado',
                default    => ucwords($data->status),
            };
            $statusStyle = match($data->status) {
                'Open'     => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'Started'  => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'Closed', 'Rated' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                default    => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
            };
        @endphp
        <span class="px-4 py-1.5 rounded-full text-sm font-bold border-2 shadow-md italic"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- Basic Information --}}
    <x-sidebar-section icon="ri-information-line" label="Información Básica">
        <x-sidebar-row label="Usuario"     :value="$data->username" />
        <x-sidebar-row label="Fecha"       :value="$data->createdAt" />
        <x-sidebar-row label="Sede"        :value="$data->facility" />
        <x-sidebar-row label="Iniciado"    :value="$data->startedAt" />
        <x-sidebar-row label="Cerrado"     :value="$data->closedAt" />
        @if($data->rating)
            <x-sidebar-row label="Calificación" :value="$data->rating . ' / 5'" />
        @endif

        {{-- Evidence --}}
        @if(count($data->evidences) > 0)
            <div class="flex text-[10px] mt-2 items-start border-t pt-2" style="border-color:var(--b)">
                <div class="w-20 opacity-50 font-black uppercase shrink-0">Evidencias:</div>
                <div class="flex-1 space-y-1">
                    @foreach($data->evidences as $evidence)
                        <a href="{{ $evidence['url'] }}" target="_blank" class="flex items-center gap-1 text-blue-500 hover:underline font-bold">
                            <i class="ri-file-line text-sm"></i> Evidencia
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </x-sidebar-section>

    {{-- Description --}}
    <x-sidebar-section icon="ri-file-text-line" label="Descripción">
        <div class="text-[11px] leading-relaxed opacity-90 p-3 rounded-xl border italic" 
             style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            {{ $data->description }}
        </div>
    </x-sidebar-section>

    {{-- Asset --}}
    @php 
        $canEdit = $data->status !== 'Closed' && $data->status !== 'Rejected' && $data->status !== 'Rated'; 
        
        // Mapeo de opciones para los selects (tomando de SidebarData)
        $assetOptions = collect($data->assets)->pluck('label', 'id')->toArray();
        $assigneeOptions = collect($data->assignees)->pluck('name', 'id')->toArray();
    @endphp
    
    <x-sidebar-section icon="ri-shield-user-line" label="Activo Relacionado">
        @if($canEdit)
            <x-form-field 
                :field="new Field(name: 'asset_id', label: 'Activo', widget: 'slimselect', options: $assetOptions, placeholder: 'Buscar activo...')"
                :value="$data->assetId"
                :hasLabel="false"
                :hxPost="route('tickets.patch', $data->id)"
                :hxVals="['field' => 'asset_id']"
            />
        @else
            <div class="text-[11px] font-bold p-2 rounded bg-sigma-bg2 border border-sigma-b">
                {{ $assetOptions[$data->assetId] ?? 'No Vinculado' }}
            </div>
        @endif
    </x-sidebar-section>

    <div class="grid grid-cols-2 gap-3">
        {{-- Priority --}}
        <x-sidebar-section icon="ri-flag-line" label="Prioridad">
            @if($canEdit)
                <x-form-field 
                    :field="new Field(name: 'priority', label: 'Prioridad', type: 'select', options: ['High' => 'Alta', 'Medium' => 'Media', 'Low' => 'Baja'])"
                    :value="$data->priority"
                    :hasLabel="false"
                    :hxPost="route('tickets.patch', $data->id)"
                    :hxVals="['field' => 'priority']"
                />
            @else
                <div class="text-[11px] font-bold">{{ $data->priority ?? '-' }}</div>
            @endif
        </x-sidebar-section>

        {{-- Assignment --}}
        <x-sidebar-section icon="ri-user-settings-line" label="Asignación">
            @if($canEdit)
                <x-form-field 
                    :field="new Field(name: 'assignee_id', label: 'Asignado', type: 'select', options: $assigneeOptions, placeholder: 'Seleccionar técnico...')"
                    :value="$data->assigneeId"
                    :hasLabel="false"
                    :hxPost="route('tickets.patch', $data->id)"
                    :hxVals="['field' => 'assignee_id']"
                />
            @else
                <div class="text-[11px] font-bold">
                    {{ $assigneeOptions[$data->assigneeId] ?? 'No Asignado' }}
                </div>
            @endif
        </x-sidebar-section>
    </div>

    <div class="grid grid-cols-2 gap-3 pb-4">
        {{-- SGC --}}
        <x-sidebar-section icon="ri-settings-3-line" label="Categoría SGC">
            <x-form-field 
                :field="new Field(name: 'sgc', label: 'SGC', type: 'select', options: ['Corrective' => 'Correctivo', 'Preventive' => 'Preventivo', 'Production' => 'Producción', 'Infrastructure' => 'Infraestructura'])"
                :value="$data->sgc"
                :hasLabel="false"
                :hxPost="route('tickets.patch', $data->id)"
                :hxVals="['field' => 'sgc']"
            />
        </x-sidebar-section>

        {{-- Root Cause --}}
        <x-sidebar-section icon="ri-error-warning-line" label="Causa Raíz">
            <x-form-field 
                :field="new Field(name: 'root_cause', label: 'Causa', type: 'select', options: array_combine(['Habituales', 'Falla Eléctrica', 'Falta Capacitación', 'Desgaste', 'Mal Uso'], ['Habituales', 'Falla Eléctrica', 'Falta Capacitación', 'Desgaste', 'Mal Uso']))"
                :value="$data->rootCause"
                :hasLabel="false"
                :hxPost="route('tickets.patch', $data->id)"
                :hxVals="['field' => 'root_cause']"
            />
        </x-sidebar-section>
    </div>
</div>
