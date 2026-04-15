<div class="p-5 space-y-5" x-data="ppeDelivery()">

    {{-- Employee --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--tx2)">
            Empleado
        </label>
        <select name="employee_id" id="employee_id" data-widget="slimselect"
                data-url="{{ route('ppe.options.employees') }}"
                class="w-full" required>
            <option value="">Seleccionar empleado…</option>
        </select>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--tx2)">
            Notas
        </label>
        <textarea name="notes" rows="2"
                  class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                  style="background:var(--bg2); border-color:var(--b); color:var(--tx); resize:none"
                  placeholder="Observaciones opcionales…"></textarea>
    </div>

    {{-- PPE Items table --}}
    @if(count($items) > 0)
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--tx2)">
            Tipo de entrega por EPP
        </p>
        <div class="rounded-xl border overflow-hidden" style="border-color:var(--b)">
            <table class="w-full text-xs">
                <thead>
                    <tr style="background:var(--bg2); color:var(--tx2)">
                        <th class="text-left px-3 py-2 font-semibold uppercase tracking-wider">EPP</th>
                        <th class="px-2 py-2 font-semibold uppercase tracking-wider text-center">Dotación</th>
                        <th class="px-2 py-2 font-semibold uppercase tracking-wider text-center">Reposición</th>
                        <th class="px-2 py-2 font-semibold uppercase tracking-wider text-center">Perdida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="border-t" style="border-color:var(--b)">
                        <td class="px-3 py-2 font-medium" style="color:var(--tx)">{{ $item->name }}</td>
                        @foreach(['Dotación', 'Reposición', 'Perdida'] as $kind)
                        <td class="px-2 py-2 text-center">
                            <input type="radio"
                                   name="type[{{ $item->id }}]"
                                   value="{{ $kind }}"
                                   class="accent-[var(--ac)] w-4 h-4 cursor-pointer">
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Signature Pad --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--tx2)">
            Firma del empleado
        </p>
        <div class="relative rounded-xl border overflow-hidden" style="border-color:var(--b); background:var(--bg2)">
            <canvas id="signature-canvas"
                    width="600" height="180"
                    class="w-full cursor-crosshair touch-none"
                    style="background:#fff"
                    x-ref="canvas"></canvas>
        </div>
        <div class="flex gap-2 mt-2">
            <button type="button"
                    @click="clearSignature()"
                    class="text-xs px-3 py-1.5 rounded-lg border font-semibold"
                    style="border-color:var(--b); color:var(--tx2); background:var(--bg2)">
                <i class="ri-eraser-line mr-1"></i> Limpiar
            </button>
        </div>
        <input type="hidden" name="signature_base64" x-ref="sigInput">
    </div>

    {{-- Submit --}}
    <div class="pt-2">
        <button type="button"
                @click="submit()"
                class="w-full py-2.5 rounded-xl font-bold text-sm transition"
                style="background:var(--ac); color:var(--ac-tx)">
            <i class="ri-save-line mr-1"></i> Guardar Entrega
        </button>
    </div>

</div>

<script>
function ppeDelivery() {
    return {
        pad: null,
        init() {
            const canvas = this.$refs.canvas;
            let drawing = false;
            let lastX = 0, lastY = 0;
            const ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#1a1a1a';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';

            const pos = (e) => {
                const r = canvas.getBoundingClientRect();
                const scaleX = canvas.width / r.width;
                const scaleY = canvas.height / r.height;
                const src = e.touches ? e.touches[0] : e;
                return { x: (src.clientX - r.left) * scaleX, y: (src.clientY - r.top) * scaleY };
            };

            canvas.addEventListener('mousedown',  e => { drawing = true; const p = pos(e); lastX = p.x; lastY = p.y; });
            canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = pos(e); ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke(); lastX = p.x; lastY = p.y; });
            canvas.addEventListener('mouseup',    () => drawing = false);
            canvas.addEventListener('mouseleave', () => drawing = false);
            canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = pos(e); lastX = p.x; lastY = p.y; }, { passive: false });
            canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; const p = pos(e); ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y); ctx.stroke(); lastX = p.x; lastY = p.y; }, { passive: false });
            canvas.addEventListener('touchend',   () => drawing = false);
        },
        clearSignature() {
            const canvas = this.$refs.canvas;
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            this.$refs.sigInput.value = '';
        },
        submit() {
            const canvas = this.$refs.canvas;
            this.$refs.sigInput.value = canvas.toDataURL('image/jpeg', 0.85);
            const form = new FormData();
            const container = this.$el;
            container.querySelectorAll('[name]').forEach(el => {
                if (el.type === 'radio' && !el.checked) return;
                if (el.name) form.append(el.name, el.value);
            });
            htmx.ajax('POST', '{{ route("ppe.deliveries.save") }}', {
                values: Object.fromEntries(form),
                target: '#modal-body',
                swap: 'none',
            });
        },
    };
}
</script>
