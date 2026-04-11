@props([
    'route',
    'id',
    'collection' => 'gallery',
    'label' => 'Subir Archivos',
    'icon' => 'ri-upload-2-line'
])

<form hx-post="{{ route('shared.upload', ['route' => $route, 'id' => $id]) }}"
      hx-encoding="multipart/form-data"
      hx-swap="none"
      x-data="{ dragging: false, uploading: false }"
      @htmx:before-request="uploading = true"
      @htmx:after-request="uploading = false; $el.reset()"
      class="relative group">
    
    <input type="hidden" name="collection" value="{{ $collection }}">
    
    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer transition-all duration-300"
           :class="dragging ? 'border-neutral-900 bg-neutral-50' : 'border-neutral-200 hover:border-neutral-400 bg-white'"
           @dragover.prevent="dragging = true"
           @dragleave.prevent="dragging = false"
           @drop="dragging = false">
        
        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!uploading">
            <i class="{{ $icon }} text-2xl mb-2 text-neutral-400 group-hover:text-neutral-900 transition-colors"></i>
            <p class="text-xs font-bold uppercase tracking-wider text-neutral-500 group-hover:text-neutral-900 transition-colors">
                {{ $label }}
            </p>
            <p class="text-[10px] text-neutral-400 mt-1">Arrastra o haz clic para seleccionar</p>
        </div>

        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="uploading" x-cloak>
            <svg class="animate-spin h-6 w-6 text-neutral-900 mb-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-xs font-bold uppercase tracking-widest text-neutral-900 animate-pulse">Subiendo...</p>
        </div>

        <input type="file" name="files[]" multiple
               class="hidden"
               @change="$el.form.dispatchEvent(new Event('submit'))" />
    </label>
</form>
