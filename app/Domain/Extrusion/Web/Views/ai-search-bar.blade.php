{{-- AI Natural Language Search Bar for Extrusion --}}
<div class="mx-4 mt-3 flex gap-2" x-data="extrusionAiSearch()">
    <div class="relative flex-1">
        <i class="ri-sparkling-2-line absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--ac)"></i>
        <input x-ref="input" type="text"
               placeholder="Busca en lenguaje natural: ej. 'ES Cover b mayor a 2 h entre 1 y 3 e1 menor a 0.5'"
               @keydown.enter="search()"
               class="w-full pl-8 pr-4 py-2 rounded-lg border text-xs focus:outline-none focus:ring-1"
               style="background:var(--bg2); border-color:var(--b); color:var(--tx); --tw-ring-color:var(--ac)">
    </div>
    <button @click="search()"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition"
            style="background:var(--ac); color:var(--ac-tx)">
        <i class="ri-robot-line"></i>
        <span x-text="loading ? 'Buscando…' : 'AI Search'"></span>
    </button>
    <button @click="clear()" title="Volver a vista normal"
            class="px-3 py-2 rounded-lg text-xs border transition"
            style="border-color:var(--b); color:var(--tx2); background:var(--bg2)">
        <i class="ri-close-line"></i>
    </button>
</div>

<script>
function extrusionAiSearch() {
    return {
        loading: false,
        aiMode: false,
        search() {
            const q = this.$refs.input.value.trim();
            if (!q) return;
            this.loading = true;
            fetch(`/extrusion/ai-search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(params => {
                    this.loading = false;
                    if (!params || !Object.keys(params).length) {
                        alert('No pude interpretar la búsqueda, intenta con otros términos.');
                        return;
                    }
                    this.aiMode = true;
                    const table = document.querySelector('#dt_extrusion')?.tabulator;
                    if (table) {
                        table.setData(`/extrusion/ai-data?params=${encodeURIComponent(JSON.stringify(params))}`);
                    }
                })
                .catch(() => { this.loading = false; });
        },
        clear() {
            this.$refs.input.value = '';
            this.aiMode = false;
            const table = document.querySelector('#dt_extrusion')?.tabulator;
            if (table) {
                table.setData('/extrusion/data');
                table.clearHeaderFilter();
            }
        },
    };
}
</script>
