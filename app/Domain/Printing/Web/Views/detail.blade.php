<div class="w-[98%] max-h-[95vh] rounded-xl shadow-2xl relative z-50 flex flex-col overflow-hidden"
     style="background:var(--bg); color:var(--tx)">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:var(--b)">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg" style="background:var(--ac)">
                <i class="ri-printer-fill text-lg" style="color:var(--ac-inv)"></i>
            </div>
            <div>
                <h1 class="text-sm font-extrabold uppercase tracking-widest" style="color:var(--tx)">
                    {{ $wo->code }}
                    <span class="opacity-40 mx-1">|</span>
                    Print Labels
                </h1>
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-50">Work Orders</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button id="printTicketsBtn"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="background:var(--ac); color:var(--ac-inv)">
                <i class="ri-printer-line text-sm"></i>
                <span class="hidden sm:inline">Print Labels</span>
            </button>

            <button
                hx-delete="{{ route('printing.delete', $wo->code) }}"
                hx-confirm="¿Eliminar WO {{ $wo->code }}? Esta acción no se puede deshacer."
                hx-indicator="#global-loader"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-[0.98] bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white">
                <i class="ri-delete-bin-line text-sm"></i>
                <span class="hidden sm:inline">Delete</span>
            </button>

            <button onclick="window.dispatchEvent(new CustomEvent('close-modal'))"
                class="p-2 rounded-lg transition-all hover:opacity-70" style="color:var(--tx2)">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="flex-1 min-h-0 p-4">
        <div id="wo-items-table"
             data-widget="tabulator"
             data-config='@json([
                 "data"     => $items,
                 "layout"   => "fitColumns",
                 "height"   => "450px",
                 "selectable" => $isEsId,
                 "columns"  => [
                     $isEsId ? [
                         "titleFormatter" => "rowSelection",
                         "formatter"      => "rowSelection",
                         "hozAlign"       => "center",
                         "headerHozAlign" => "center",
                         "headerSort"     => false,
                         "width"          => 50,
                     ] : null,
                     !$isEsId ? [
                         "title"                 => "Print Qty",
                         "field"                 => "print_val",
                         "width"                 => 110,
                         "hozAlign"              => "center",
                         "editor"                => "number",
                         "editorParams"          => ["min" => 0],
                         "headerFilter"          => "tickCross",
                         "headerFilterParams"    => ["tristate" => false],
                         "headerFilterFunc"      => "function() { return true; }",
                         "formatter"             => "function(cell) { return \"<div>\" + (cell.getValue() || 0) + \"</div>\"; }",
                     ] : null,
                     ["title" => "Part #",      "field" => "id",          "width" => 140, "headerFilter" => "input"],
                     ["title" => "Description", "field" => "description", "headerFilter" => "input", "formatter" => "textarea"],
                     ["title" => "Finish / UC", "field" => "fuc",         "headerFilter" => "input"],
                     ["title" => "Stock",       "field" => "qty",         "width" => 80, "hozAlign" => "center"],
                 ],
             ])'
             class="w-full text-xs rounded-lg border" style="border-color:var(--b)"></div>
    </div>

    {{-- Print loader overlay --}}
    <div id="printLoader"
         class="absolute inset-0 hidden items-center justify-center z-50"
         style="background:color-mix(in srgb,var(--bg) 85%,transparent);backdrop-filter:blur(4px)">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 rounded-full border-4 animate-spin"
                 style="border-color:var(--b); border-top-color:var(--ac)"></div>
            <span class="text-[10px] font-black uppercase tracking-widest" style="color:var(--tx2)">
                Preparing print…
            </span>
        </div>
    </div>
</div>

<script>
(function () {
    const isEsId    = @json($isEsId);
    const woCode    = @json($wo->code);
    const esIdValue = @json($wo->es_id ?? '');
    const loader    = document.getElementById('printLoader');
    const url       = isEsId
        ? '{{ route("printing.print.es", $wo->code) }}'
        : '{{ route("printing.print.esm", $wo->code) }}';

    document.getElementById('printTicketsBtn').addEventListener('click', () => {
        const table = document.getElementById('wo-items-table')?.tabulator;
        if (!table) return;

        const payload = new URLSearchParams();
        payload.append('woId', woCode);

        if (isEsId) {
            const selected = table.getSelectedData();
            if (!selected.length) { alert('Select at least one item.'); return; }
            selected.forEach(r => {
                payload.append('id[]', r.id);
                payload.append('marca[]', r.description);
                payload.append('esid[]', esIdValue);
            });
        } else {
            const rows = table.getData().filter(r => r.print_val > 0);
            if (!rows.length) { alert('Enter at least one quantity.'); return; }
            rows.forEach(r => {
                payload.append('id[]', r.id);
                payload.append('val[]', r.print_val);
            });
        }

        loader.classList.remove('hidden');
        loader.classList.add('flex');

        const win = window.open('about:blank');
        if (!win) {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            alert('Allow pop-ups to print labels.');
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: payload.toString(),
        })
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.text(); })
        .then(html => {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            win.document.open();
            win.document.write(html);
            win.document.close();
        })
        .catch(err => {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            win.document.open();
            win.document.write(`<body style="font-family:sans-serif;padding:40px;color:red"><h2>Error</h2><p>${err.message}</p></body>`);
            win.document.close();
        });
    });
})();
</script>
