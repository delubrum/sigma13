<form id="sigma-new-form"
      x-data="{ loading: false, _deps: {}, _track(name, val) { this._deps[name] = val } }"
      @submit="loading = true"
      @htmx:after-request="loading = false"
      hx-{{ $method ?? 'post' }}="{{ $customPostRoute ?? "/{$route}/upsert" }}"
      hx-target="{{ $target ?? '#modal-body' }}"
      hx-swap="innerHTML"

      @if(isset($config->multipart) && $config->multipart)
      hx-encoding="multipart/form-data"
      enctype="multipart/form-data"
      @endif
    class="grid grid-cols-1 lg:grid-cols-4 gap-4">

    @csrf
    @honeypot
    @php
        $loopData = is_array($data) ? $data : (method_exists($data, 'toArray') ? $data->toArray() : $data);
    @endphp

    @foreach($loopData ?? [] as $key => $val)
        @if(!is_array($val))
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif
    @endforeach

    @foreach ($config->formFields as $field)
        @php
            $colSpan = match($field->width->value ?? $field->width) {
                3 => 'col-span-1 lg:col-span-3',
                2 => 'col-span-1 lg:col-span-2',
                1 => 'col-span-1 lg:col-span-1',
                default => 'col-span-1 lg:col-span-4',
            };
        @endphp
        <x-form-field 
            :field="$field" 
            :data="$data" 
            :class="$colSpan" 
        />
    @endforeach

    <div class="col-span-1 lg:col-span-4 flex justify-end gap-3 pt-2 mt-2 border-t" style="border-color:var(--b)">
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('{{ $closeEvent ?? 'close-modal' }}'))"
                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="border-color:var(--b); color:var(--tx2)">
            Cancelar
        </button>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="background:var(--ac); color:var(--ac-inv)">
            <i class="ri-save-line text-sm"></i>
            <span>Guardar</span>
        </button>
    </div>

</form>

