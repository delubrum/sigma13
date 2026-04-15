<div class="space-y-4">
    <div class="relative h-64 overflow-hidden rounded-t-lg bg-white p-4">
        @if($data->imgUrl)
            <img src="{{ $data->imgUrl }}" class="w-full h-full object-contain mx-auto" alt="{{ $data->title }}">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-sigma-tx2 opacity-20">
                <i class="ri-image-2-line text-6xl"></i>
                <span class="text-[10px] font-black uppercase">Sin Imagen</span>
            </div>
        @endif
        
        <input type="file"
               class="absolute inset-0 opacity-0 cursor-pointer z-10"
               hx-post="{{ route('shared.upload', ['route' => 'fasteners', 'id' => $data->id, 'collection' => 'profile']) }}"
               hx-encoding="multipart/form-data"
               hx-trigger="change"
               hx-indicator="#global-loader"
               hx-on::after-request="if(event.detail.successful) htmx.trigger('body', 'refresh')"
               accept="image/*"
               name="file">
    </div>

    <div class="px-4 pb-4 space-y-4">
        <div class="flex justify-center -mt-8 relative z-20">
            <span class="px-4 py-1 rounded-full text-[10px] font-black border-2 shadow-sm uppercase bg-white"
                  style="color:var(--ac); border-color:var(--ac)">
                {{ $data->subtitle }}
            </span>
        </div>

        <x-sidebar-section icon="ri-settings-line" label="Especificaciones">
            @foreach ($data->properties as $prop)
                <x-sidebar-row :label="$prop->label" :value="$prop->value" />
            @endforeach
        </x-sidebar-section>

        @if($data->model->observation)
            <x-sidebar-section icon="ri-chat-3-line" label="Observaciones">
                <div class="text-xs p-2 rounded border bg-sigma-bg" style="border-color:var(--b); color:var(--tx2)">
                    {{ $data->model->observation }}
                </div>
            </x-sidebar-section>
        @endif
    </div>
</div>
