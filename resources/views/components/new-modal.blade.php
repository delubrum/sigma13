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

    @foreach ($config->formFields as $field)
        @php
            $colSpan = match($field->width) {
                \App\Data\Shared\FieldWidth::ThreeQuarters => 'col-span-4 sm:col-span-3',
                \App\Data\Shared\FieldWidth::Half => 'col-span-4 sm:col-span-2',
                \App\Data\Shared\FieldWidth::Quarter => 'col-span-4 sm:col-span-1',
                default => 'col-span-4 sm:col-span-4',
            };
            $inputId = 'field-' . $field->name;
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
                    @foreach ($field->options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'select')
                <select id="{{ $inputId }}"
                        name="{{ $field->name }}"
                        {{ $field->required ? 'required' : '' }}
                        class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border"
                        style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
                    <option value="">— Seleccionar —</option>
                    @foreach ($field->options as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

            @elseif ($field->type === 'textarea')
                <textarea id="{{ $inputId }}"
                          name="{{ $field->name }}"
                          rows="3"
                          placeholder="{{ $field->placeholder }}"
                          {{ $field->required ? 'required' : '' }}
                          class="w-full px-3 py-2 rounded-lg text-sm outline-none transition-all border resize-none"
                          style="background:var(--bg2); border-color:var(--b); color:var(--tx)"></textarea>

            @else
                <input id="{{ $inputId }}"
                       type="{{ $field->type }}"
                       name="{{ $field->name }}"
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
