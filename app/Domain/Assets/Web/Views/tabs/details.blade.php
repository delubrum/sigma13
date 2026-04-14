{{-- Adquisición --}}
<div class="mb-5">
    <h2 class="text-base font-bold mb-3 flex items-center gap-1.5" style="color:var(--tx)">
        <i class="ri-information-line text-xl"></i>
        <span>Detalles</span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Fecha Adquisición</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $details->acquisition_date }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Costo Adquisición</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">$ {{ $details->price }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Proveedor</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $details->supplier }}</div>
        </div>
        <div class="rounded-md p-3 border" style="background:var(--bg2); border-color:var(--b)">
            <div class="text-xs mb-1" style="color:var(--tx2)">Factura No.</div>
            <div class="text-sm font-semibold" style="color:var(--tx)">{{ $details->invoice }}</div>
        </div>
    </div>
</div>

{{-- Especificaciones Técnicas --}}
<div class="mb-5">
    <h2 class="text-base font-bold mb-3 flex items-center gap-1.5" style="color:var(--tx)">
        <i class="ri-mac-line text-xl"></i>
        <span>Especificaciones Técnicas</span>
    </h2>
    <table class="w-full border-collapse rounded-md overflow-hidden text-xs" style="border:1px solid var(--b)">
        <thead>
            <tr style="background:var(--bg2)">
                <th class="px-3 py-2 text-left font-semibold" style="color:var(--tx2)">Característica</th>
                <th class="px-3 py-2 text-left font-semibold" style="color:var(--tx2)">Detalle</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Procesador</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $details->cpu }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Memoria RAM</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $details->ram }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Almacenamiento</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">SSD1: {{ $details->ssd }} / SSD2: {{ $details->hdd }}</td>
            </tr>
            <tr style="border-top:1px solid var(--b)">
                <td class="px-3 py-2 font-medium" style="color:var(--tx2); background:var(--bg2)">Sistema Operativo</td>
                <td class="px-3 py-2 font-semibold" style="color:var(--tx)">{{ $details->so }}</td>
            </tr>
        </tbody>
    </table>
</div>
