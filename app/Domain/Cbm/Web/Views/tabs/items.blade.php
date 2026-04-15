@php
/** @var \App\Domain\Cbm\Models\Cbm $model */
$items = $model->items;
@endphp

<div class="space-y-4">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xs font-black uppercase tracking-widest opacity-50" style="color:var(--tx2)">Listado de Items Procesados</h3>
        <span class="px-3 py-1 rounded bg-black text-white text-[10px] font-black uppercase tracking-widest">{{ $items->count() }} Items</span>
    </div>

    <div class="overflow-hidden rounded-xl border" style="border-color:var(--b)">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-[9px] font-black tracking-widest" style="color:var(--tx2); border-bottom:1px solid var(--b)">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Ancho (″)</th>
                    <th class="p-3">Alto (″)</th>
                    <th class="p-3">Largo (″)</th>
                    <th class="p-3">Peso (LB)</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:var(--b)">
                @foreach($items as $idx => $it)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-3 font-bold opacity-50">{{ $idx + 1 }}</td>
                    <td class="p-3 font-bold italic">{{ number_format($it->width, 1) }}</td>
                    <td class="p-3 font-bold italic">{{ number_format($it->height, 1) }}</td>
                    <td class="p-3 font-bold italic">{{ number_format($it->item_length, 1) }}</td>
                    <td class="p-3 font-bold text-red-600 italic">{{ number_format($it->weight, 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
