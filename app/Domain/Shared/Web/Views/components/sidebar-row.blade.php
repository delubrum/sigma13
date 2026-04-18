@props(['label', 'value' => null, 'id' => null, 'url' => null, 'icon' => null])

@if($value)
<div class="flex text-xs gap-1" @if($id) id="{{ $id }}" @endif>
    <span class="w-24 shrink-0" style="color:var(--tx2)">{{ $label }}:</span>
    @if($url)
        <a href="{{ $url }}" target="_blank" rel="noopener"
           class="font-semibold flex items-center gap-1 hover:underline truncate"
           style="color:var(--info)">
            @if($icon)<i class="{{ $icon }} text-sm"></i>@endif
            {{ Str::ucfirst((string)$value) }}
        </a>
    @else
        <span class="font-semibold truncate" style="color:var(--tx)">{{ Str::ucfirst((string)$value) }}</span>
    @endif
</div>
@endif