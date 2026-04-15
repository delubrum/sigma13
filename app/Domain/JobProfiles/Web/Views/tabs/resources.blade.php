@php
$decoded = json_decode($content ?? '[]', true);
if (! is_array($decoded)) { $decoded = []; }

function jpIsActive(string $group, string $name, array $data): bool {
    if (isset($data[$group]['items']) && is_array($data[$group]['items'])) {
        return in_array($name, $data[$group]['items'], true);
    }
    if (array_values($data) === $data) {
        return in_array($name, $data, true);
    }
    return false;
}

function jpOtro(string $group, array $data): string {
    return (string) ($data[$group]['otro'] ?? '');
}

$groups = [
    ['key' => 'Recursos',              'label' => 'Recursos',               'icon' => 'ri-file-text-line',  'items' => $assets],
    ['key' => 'InformacionConfidencial','label' => 'Información Confidencial','icon' => 'ri-lock-line',       'items' => ['Administrativa', 'Comercio exterior', 'Compras', 'Tesorería'], 'hasOtro' => true],
    ['key' => 'Maquinaria',            'label' => 'Maquinaria',             'icon' => 'ri-tools-line',       'items' => ['Punzonadoras', 'Dobladoras', 'Cortadoras', 'Otros']],
    ['key' => 'Inventario',            'label' => 'Inventario',             'icon' => 'ri-archive-line',     'items' => ['Otros']],
    ['key' => 'ManejoDeValores',       'label' => 'Manejo de Valores',      'icon' => 'ri-bank-card-line',   'items' => ['Dinero en efectivo', 'Chequeras', 'Tarjetas de Crédito']],
];
@endphp

<div class="space-y-4 p-1">
    @foreach($groups as $group)
    <div class="pb-3 border-b border-dashed last:border-0 last:pb-0" style="border-color:var(--b)">
        <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
            <i class="{{ $group['icon'] }} text-base"></i>
            <span class="uppercase tracking-wider">{{ $group['label'] }}</span>
        </h3>
        <div class="flex flex-wrap gap-1.5">
            @foreach($group['items'] as $item)
                @php $active = jpIsActive($group['key'], $item, $decoded); @endphp
                <button type="button"
                    class="text-xs px-2.5 py-1 rounded-full border transition-colors {{ $canEdit ? 'cursor-pointer' : 'cursor-default' }}"
                    style="{{ $active
                        ? 'background:var(--tx); color:var(--bg); border-color:var(--tx)'
                        : 'background:var(--bg2); color:var(--tx2); border-color:var(--b)' }}"
                    @if($canEdit)
                        hx-post="{{ route('job-profiles.save-resource') }}"
                        hx-vals='@json(['id' => $jpId, 'group' => $group['key'], 'value' => $item])'
                        hx-swap="none"
                        onclick="var s=this.style; var on=s.background.includes('var(--tx)'); s.background=on?'var(--bg2)':'var(--tx)'; s.color=on?'var(--tx2)':'var(--bg)'; s.borderColor=on?'var(--b)':'var(--tx)';"
                    @endif
                >{{ $item }}</button>
            @endforeach

            @if(!empty($group['hasOtro']))
                <input type="text"
                    class="text-xs px-2.5 py-1 rounded-full border"
                    style="background:var(--bg2); color:var(--tx); border-color:var(--b); min-width:120px"
                    placeholder="Otros..."
                    value="{{ jpOtro($group['key'], $decoded) }}"
                    @if($canEdit)
                        hx-post="{{ route('job-profiles.save-resource') }}"
                        hx-trigger="change, keyup delay:800ms"
                        hx-swap="none"
                        hx-vals='@json(['id' => $jpId, 'group' => $group['key'], 'is_input' => 'true'])'
                        name="value"
                    @else
                        disabled
                    @endif
                />
            @endif
        </div>
    </div>
    @endforeach
</div>
