<form id="sigma-new-form"
      hx-post="/{{ $route }}"
      hx-target="{{ $target ?? '#modal-body' }}"
      hx-swap="innerHTML"
      @if(isset($config->multipart) && $config->multipart)
      hx-encoding="multipart/form-data"
      enctype="multipart/form-data"
      @endif
      class="grid grid-cols-1 sm:grid-cols-4 gap-4">

    @csrf
    @if(isset($data['id']))
        <input type="hidden" name="id" value="{{ $data['id'] }}">
    @endif

    @foreach ($config->formFields as $field)
        @php
            $colSpan = match($field->width) {
                3 => 'col-span-1 sm:col-span-3',
                2 => 'col-span-1 sm:col-span-2',
                1 => 'col-span-1 sm:col-span-1',
                default => 'col-span-1 sm:col-span-4',
            };
            $inputId = 'field-' . $field->name;
            $value = $data[$field->name] ?? '';
            // Handle Carbon dates if necessary
            if ($value instanceof \Carbon\Carbon) {
                $value = $value->format($field->type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s');
            }
        @endphp

        <div class="flex flex-col gap-1 {{ $colSpan }}">

            <label for="{{ $inputId }}"
                   class="text-[10px] font-black uppercase tracking-widest"
                   style="color:var(--tx2)">
                {{ $field->label }}
                @if ($field->required)<span style="color:var(--ac)">*</span>@endif
            </label>

            @if ($field->widget === 'filepond')
                <input type="file"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       data-widget="filepond"
                       {{ $field->required ? 'required' : '' }}>

            @elseif ($field->widget === 'flatpickr')
                <input type="text"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       value="{{ $value }}"
                       placeholder="{{ $field->placeholder ?: 'Seleccionar fecha' }}"
                       data-widget="flatpickr"
                       autocomplete="off"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">

            @elseif ($field->widget === 'flatpickr-range')
                <input type="text"
                       id="{{ $inputId }}"
                       name="{{ $field->name }}"
                       value="{{ $value }}"
                       placeholder="{{ $field->placeholder ?: 'Rango de fechas' }}"
                       data-widget="flatpickr-range"
                       autocomplete="off"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">

            @elseif ($field->widget === 'slimselect')
                <select id="{{ $inputId }}"
                        name="{{ $field->name }}"
                        data-widget="slimselect"
                        {{ $field->required ? 'required' : '' }}>
                    <option value="">— Seleccionar —</option>
                    @foreach ($field->options as $optValue => $optLabel)
                        <option value="{{ $optValue }}" @if($value == $optValue) selected @endif>{{ $optLabel }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'select')
                <select id="{{ $inputId }}"
                        name="{{ $field->name }}"
                        {{ $field->required ? 'required' : '' }}
                        class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                        style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
                    <option value="">— Seleccionar —</option>
                    @foreach ($field->options as $optValue => $optLabel)
                        <option value="{{ $optValue }}" @if($value == $optValue) selected @endif>{{ $optLabel }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'textarea')
                <textarea id="{{ $inputId }}"
                          name="{{ $field->name }}"
                          rows="3"
                          placeholder="{{ $field->placeholder }}"
                          {{ $field->required ? 'required' : '' }}
                          class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border resize-none"
                          style="background:var(--bg2); border-color:var(--b); color:var(--tx)">{{ $value }}</textarea>

            @else
                <input id="{{ $inputId }}"
                       type="{{ $field->type }}"
                       name="{{ $field->name }}"
                       value="{{ $value }}"
                       placeholder="{{ $field->placeholder }}"
                       {{ $field->required ? 'required' : '' }}
                       class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            @endif

            @if ($field->hint)
                <p class="text-[10px]" style="color:var(--tx2); opacity:.5">{{ $field->hint }}</p>
            @endif

        </div>
    @endforeach

    <div class="col-span-4 flex justify-end gap-3 pt-2 mt-2 border-t" style="border-color:var(--b)">
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

{{-- MÁGICA OOB: Actualiza la cabecera del layout desde la respuesta del servidor --}}
<div id="modal-icon{{ $suffix ?? '' }}" hx-swap-oob="true" class="p-2 rounded-lg shadow-md" style="background:var(--ac)">
    <i class="{{ $config->icon }} text-xl" style="color:var(--ac-inv)"></i>
</div>

<h1 id="modal-title{{ $suffix ?? '' }}" hx-swap-oob="true" class="text-xl font-extrabold uppercase tracking-tight" style="color:var(--tx)">
    {{ $config->title }}
</h1>

<p id="modal-subtitle{{ $suffix ?? '' }}" hx-swap-oob="true" class="text-[10px] uppercase font-bold tracking-widest" style="color:var(--tx2); opacity:.6">
    {{ $config->subtitle }}
</p>
