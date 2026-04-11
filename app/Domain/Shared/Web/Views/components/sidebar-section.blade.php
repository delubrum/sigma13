{{-- resources/views/components/sigma/sidebar-section.blade.php --}}
@props(['icon', 'label'])

<div class="pb-3 border-b border-dashed last:border-0 last:pb-0" style="border-color:var(--b)">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
        <i class="{{ $icon }} text-base"></i>
        <span class="uppercase tracking-wider">{{ $label }}</span>
    </h3>
    {{ $slot }}
</div>