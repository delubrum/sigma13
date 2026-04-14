@props([
    'field',
    'value' => null,
    'data' => [],
    'hasLabel' => true,
    'hxPost' => null,
    'hxTrigger' => null,
    'hxVals' => null,
    'class' => '',
])

@php
    $inputId = 'field-' . $field->name . '-' . ($data['id'] ?? Str::random(4));
    $value = $value ?? ($data[$field->name] ?? '');
    
    if ($value instanceof \DateTimeInterface) {
        $value = $value->format($field->type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s');
    }

    $baseClasses = "w-full px-3 py-2 rounded-lg text-[11px] font-bold outline-none transition-all border";
    $style = "background:var(--bg2); border-color:var(--b); color:var(--tx)";
    
    $hxAttributes = '';
    if ($hxPost) {
        $hxAttributes .= " hx-post=\"$hxPost\"";
        $hxAttributes .= " hx-trigger=\"" . ($hxTrigger ?? 'change') . "\"";
        if ($hxVals) {
            $hxAttributes .= " hx-vals='" . json_encode($hxVals) . "'";
        }
    }
@endphp

<div class="flex flex-col gap-1 {{ $class }}">
    @if ($hasLabel)
        <label for="{{ $inputId }}" class="text-[10px] font-black uppercase tracking-widest" style="color:var(--tx2)">
            {{ $field->label }}
            @if ($field->required)<span style="color:var(--ac)">*</span>@endif
        </label>
    @endif

    @if ($field->widget === 'sigma-file')
        <div class="flex flex-col gap-2 p-3 rounded-xl border-2 border-dashed transition-all" style="border-color:var(--b); background:var(--bg2)">
            @if($value)
                <div class="flex items-center gap-2 mb-1">
                    <i class="ri-checkbox-circle-line text-sigma-ac"></i>
                    <span class="text-[9px] font-bold uppercase text-sigma-ac">Archivo Actual: {{ is_string($value) ? basename($value) : 'Cargado' }}</span>
                </div>
            @endif
            <input type="file" id="{{ $inputId }}" name="{{ $field->name }}" data-widget="sigma-file" @if($field->accept) accept="{{ $field->accept }}" @endif {{ ($field->required && !$value) ? 'required' : '' }} class="w-full text-[10px] font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-sigma-ac file:text-sigma-ac-inv hover:file:opacity-80 cursor-pointer" {!! $hxAttributes !!}>
        </div>

    @elseif ($field->widget === 'flatpickr' || $field->widget === 'flatpickr-range')
        <input type="text" id="{{ $inputId }}" name="{{ $field->name }}" value="{{ $value }}" placeholder="{{ $field->placeholder ?: 'Seleccionar' }}" data-widget="{{ $field->widget }}" autocomplete="off" {{ $field->required ? 'required' : '' }} class="{{ $baseClasses }}" style="{{ $style }}" {!! $hxAttributes !!}>

    @elseif ($field->widget === 'slimselect' || $field->widget === 'tomselect')
        <select id="{{ $inputId }}" name="{{ $field->name }}" data-widget="{{ $field->widget }}" {{ $field->required ? 'required' : '' }} {!! $hxAttributes !!}>
            <option value="">— {{ $field->placeholder ?: 'Seleccionar' }} —</option>
            @foreach ($field->options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @if($value == $optValue) selected @endif>{{ $optLabel }}</option>
            @endforeach
        </select>

    @elseif ($field->type === 'select')
        <select id="{{ $inputId }}" name="{{ $field->name }}" {{ $field->required ? 'required' : '' }} class="{{ $baseClasses }}" style="{{ $style }}" {!! $hxAttributes !!}>
            <option value="">— {{ $field->placeholder ?: 'Seleccionar' }} —</option>
            @foreach ($field->options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @if($value == $optValue) selected @endif>{{ $optLabel }}</option>
            @endforeach
        </select>

    @elseif ($field->type === 'textarea')
        <textarea id="{{ $inputId }}" name="{{ $field->name }}" rows="3" placeholder="{{ $field->placeholder }}" {{ $field->required ? 'required' : '' }} class="{{ $baseClasses }} resize-none" style="{{ $style }}" {!! $hxAttributes !!}>{{ $value }}</textarea>

    @else
        <input id="{{ $inputId }}" type="{{ $field->type }}" name="{{ $field->name }}" value="{{ $value }}" placeholder="{{ $field->placeholder }}" {{ $field->required ? 'required' : '' }} class="{{ $baseClasses }}" style="{{ $style }}" {!! $hxAttributes !!}>
    @endif

    @if ($field->hint)
        <p class="text-[10px]" style="color:var(--tx2); opacity:.5">{{ $field->hint }}</p>
    @endif
</div>
