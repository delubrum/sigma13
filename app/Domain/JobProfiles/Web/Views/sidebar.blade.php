<div class="p-4 space-y-4 scrollbar-thin overflow-y-auto max-h-[85vh]">

    {{-- Header --}}
    <div class="flex flex-col items-center mb-2">
        <span class="text-xs font-bold opacity-60 mb-1">{{ $data->code }}</span>
        <h2 class="text-base font-bold text-center" style="color:var(--tx)">{{ $data->name }}</h2>
        <span class="text-xs opacity-50 mt-0.5">{{ $data->division }} · {{ $data->area }}</span>
    </div>

    {{-- Basic Info --}}
    <x-sidebar-section icon="ri-information-line" label="Identificación">
        <x-sidebar-row label="Nombre"      :value="$data->name" />
        <x-sidebar-row label="Área"        :value="$data->area" />
        <x-sidebar-row label="División"    :value="$data->division" />
        <x-sidebar-row label="Reporta a"   :value="$data->reportsTo" />
        @if($data->reportsList)
            <x-sidebar-row label="Le reportan" :value="$data->reportsList" />
        @endif
        <x-sidebar-row label="Modalidad"   :value="$data->workMode" />
        <x-sidebar-row label="Nivel"       :value="$data->rank" />
        <x-sidebar-row label="Horario"     :value="$data->schedule" />
        <x-sidebar-row label="Viajes"      :value="$data->travel" />
        <x-sidebar-row label="Reloc."      :value="$data->relocation" />
        <x-sidebar-row label="Idioma"      :value="$data->lang" />
        <x-sidebar-row label="Experiencia" :value="$data->experience" />
        @if($data->obs)
            <x-sidebar-row label="Observ." :value="$data->obs" />
        @endif
        <x-sidebar-row label="Creado"      :value="$data->createdAt" />
    </x-sidebar-section>

    {{-- Mission --}}
    @if($data->mission)
        <x-sidebar-section icon="ri-flag-line" label="Misión del Cargo">
            <div class="text-[11px] leading-relaxed opacity-90 p-3 rounded-xl border italic"
                 style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
                {{ $data->mission }}
            </div>
        </x-sidebar-section>
    @endif

</div>
