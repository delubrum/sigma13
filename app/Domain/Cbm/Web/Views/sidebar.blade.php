<div class="p-6 space-y-6">
    {{-- Project Identity --}}
    <div class="flex flex-col items-center text-center space-y-3">
        <div class="w-20 h-20 rounded-2xl bg-black flex items-center justify-center shadow-lg transform -rotate-3">
            <i class="ri-box-3-line text-white text-4xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-black uppercase italic tracking-tighter" style="color:var(--tx)">{{ $data->title }}</h2>
            <div class="inline-flex px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase mt-1">
                Proyecto Activo
            </div>
        </div>
    </div>

    {{-- Specs Section --}}
    <x-sidebar-section icon="ri-bar-chart-2-line" label="Resumen de Proyecto">
        @foreach ($data->properties as $prop)
            <x-sidebar-row :label="$prop->label" :value="$prop->value" />
        @endforeach
    </x-sidebar-section>

    {{-- User Info --}}
    <x-sidebar-section icon="ri-user-smile-line" label="Responsable">
        <div class="flex items-center gap-3 p-3 rounded-xl border bg-sigma-bg" style="border-color:var(--b)">
            <div class="w-8 h-8 rounded-full bg-sigma-ac/10 flex items-center justify-center text-sigma-ac font-black text-xs uppercase">
                {{ substr($data->model->user->username ?? 'U', 0, 1) }}
            </div>
            <div>
                <span class="block text-xs font-black" style="color:var(--tx)">{{ $data->model->user->username ?? 'Unknown' }}</span>
                <span class="block text-[9px] font-bold opacity-50 uppercase tracking-widest text-sigma-tx2">Logistics Manager</span>
            </div>
        </div>
    </x-sidebar-section>

</div>
