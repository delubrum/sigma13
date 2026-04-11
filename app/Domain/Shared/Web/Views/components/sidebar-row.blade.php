{{-- resources/views/components/sigma/sidebar-row.blade.php --}}
@props(['label', 'value' => null, 'id' => null])

@if($value)
<div class="flex text-xs gap-1" @if($id) id="{{ $id }}" @endif>
    <span class="w-24 shrink-0" style="color:var(--tx2)">{{ $label }}:</span>
    <span class="font-semibold truncate" style="color:var(--tx)">{{ Str::ucfirst((string)$value) }}</span>
</div>
@endif