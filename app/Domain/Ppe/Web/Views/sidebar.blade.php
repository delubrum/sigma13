<div class="p-4 space-y-4 scrollbar-thin overflow-y-auto max-h-[85vh]">

    <x-sidebar-section icon="ri-shield-check-line" label="Ítem EPP">
        <x-sidebar-row label="ID"     :value="$data->id" />
        <x-sidebar-row label="Nombre" :value="$data->name" />
    </x-sidebar-section>

    <x-sidebar-section icon="ri-stack-line" label="Stock Actual">
        <div class="text-2xl font-black text-center py-3" style="color:var(--ac)">
            {{ $data->stock }}
        </div>
    </x-sidebar-section>

</div>
