<div class="p-4 space-y-4 scrollbar-thin overflow-y-auto max-h-[85vh]"
     x-data="extrusionSidebar({{ $data->id }})">

    {{-- Header shape --}}
    <div class="flex items-center gap-3 pb-3 border-b" style="border-color:var(--b)">
        <div class="p-2 rounded-xl" style="background:var(--ac); color:var(--ac-tx)">
            <i class="ri-layout-grid-line text-lg"></i>
        </div>
        <div>
            <p class="text-base font-black" style="color:var(--tx)">{{ $data->geometry_shape }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest" style="color:var(--tx2)">Extrusion Die</p>
        </div>
    </div>

    {{-- Company --}}
    <x-sidebar-section icon="ri-building-line" label="Company">
        <select name="company_id"
                @change="patch('company_id', $event.target.value)"
                class="w-full rounded-lg border px-2 py-1.5 text-xs focus:outline-none"
                style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            <option value=""></option>
            @foreach($data->allCompanies as $c)
                <option value="{{ $c }}" @selected($c === $data->company_id)>{{ $c }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Category --}}
    <x-sidebar-section icon="ri-price-tag-3-line" label="Category">
        <select name="category_id"
                @change="patch('category_id', $event.target.value)"
                class="w-full rounded-lg border px-2 py-1.5 text-xs focus:outline-none"
                style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            <option value=""></option>
            @foreach($data->allCategories as $c)
                <option value="{{ $c }}" @selected($c === $data->category_id)>{{ $c }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Dimensions --}}
    <x-sidebar-section icon="ri-ruler-line" label="Dimensions">
        <div class="grid grid-cols-2 gap-2">
            @foreach(['b','h','e1','e2'] as $dim)
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider mb-1" style="color:var(--tx2)">{{ strtoupper($dim) }}</label>
                <input type="number" step="0.001" name="{{ $dim }}"
                       value="{{ $data->$dim }}"
                       @change="patch('{{ $dim }}', $event.target.value)"
                       class="w-full rounded-lg border px-2 py-1.5 text-xs font-semibold focus:outline-none"
                       style="background:var(--bg2); border-color:var(--b); color:var(--tx)">
            </div>
            @endforeach
        </div>
    </x-sidebar-section>

    {{-- Clicks With --}}
    <x-sidebar-section icon="ri-links-line" label="Clicks With">
        <select name="clicks[]" multiple
                @change="patchMulti('clicks', $el)"
                class="w-full text-xs"
                style="background:var(--bg2); color:var(--tx)"
                x-ref="clicksSelect">
            @foreach($data->allShapes as $s)
                <option value="{{ $s }}" @selected(in_array($s, $data->clicks))>{{ $s }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- System / Project --}}
    <x-sidebar-section icon="ri-folders-line" label="System / Project">
        <select name="systema[]" multiple
                @change="patchMulti('systema', $el)"
                class="w-full text-xs"
                style="background:var(--bg2); color:var(--tx)"
                x-ref="systemSelect">
            @foreach($data->allSystems as $s)
                <option value="{{ $s }}" @selected(in_array($s, $data->systema))>{{ $s }}</option>
            @endforeach
        </select>
    </x-sidebar-section>

    {{-- Files --}}
    <x-sidebar-section icon="ri-attachment-2" label="Files">
        {{-- Upload --}}
        <div class="mb-3">
            <input type="file" multiple
                   x-ref="fileInput"
                   @change="uploadFiles($event)"
                   class="hidden">
            <button type="button"
                    @click="$refs.fileInput.click()"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold border transition"
                    style="border-color:var(--b); color:var(--tx2); background:var(--bg2)">
                <i class="ri-upload-2-line"></i> Upload Files
            </button>
        </div>

        {{-- File list --}}
        <div id="die-files-{{ $data->id }}" class="space-y-1">
            @foreach($data->files as $file)
            <div class="flex items-center justify-between rounded-lg px-2 py-1 border"
                 style="border-color:var(--b); background:var(--bg2)"
                 id="file-{{ md5($file['name']) }}">
                <a href="{{ $file['url'] }}" target="_blank"
                   class="text-xs font-medium truncate hover:underline" style="color:var(--ac)">
                    {{ $file['name'] }}
                </a>
                <button type="button"
                        @click="deleteFile('{{ $file['name'] }}')"
                        class="ml-2 text-xs shrink-0" style="color:var(--danger)">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
            @endforeach
            @if(count($data->files) === 0)
            <p class="text-xs italic" style="color:var(--tx2)">No files uploaded.</p>
            @endif
        </div>
    </x-sidebar-section>

</div>

<script>
function extrusionSidebar(id) {
    return {
        id,
        patch(field, value) {
            const body = new FormData();
            body.append('field', field);
            body.append(field, value);
            body.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
            fetch(`/extrusion/${id}/patch`, { method: 'POST', body })
                .then(r => r.json())
                .then(data => {
                    const trigger = data?.['HX-Trigger'] ?? null;
                    if (trigger) htmx.trigger(document.body, 'hxRefreshTables', { ids: ['dt_extrusion'] });
                });
        },
        patchMulti(field, el) {
            const selected = Array.from(el.querySelectorAll('option:checked')).map(o => o.value);
            const body = new FormData();
            body.append('field', field);
            body.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
            selected.forEach(v => body.append(field + '[]', v));
            fetch(`/extrusion/${id}/patch`, { method: 'POST', body });
        },
        uploadFiles(e) {
            const files = e.target.files;
            if (!files.length) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            Array.from(files).forEach(file => {
                const fd = new FormData();
                fd.append('file', file);
                fd.append('_token', token);
                fetch(`/extrusion/${id}/upload`, { method: 'POST', body: fd })
                    .then(() => htmx.ajax('GET', `/extrusion/${id}`, { target: '#modal-body', swap: 'innerHTML' }));
            });
        },
        deleteFile(filename) {
            if (!confirm(`Delete ${filename}?`)) return;
            const fd = new FormData();
            fd.append('filename', filename);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
            fetch(`/extrusion/${id}/delete-file`, { method: 'POST', body: fd })
                .then(() => htmx.ajax('GET', `/extrusion/${id}`, { target: '#modal-body', swap: 'innerHTML' }));
        },
    };
}
</script>
