<div class="space-y-4">
    <div class="p-4 rounded-xl border-2 shadow-sm relative overflow-hidden" style="background:var(--bg2); border-color:var(--b)">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-widest opacity-50 mb-1" style="color:var(--tx2)">Descripción del Requerimiento</h3>
                <div class="text-xs font-medium leading-relaxed" style="color:var(--tx)">{{ $id->description }}</div>
            </div>
            <div id="head-status">
                 <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm border-2" 
                      style="background:var(--bg); border-color:var(--b); color:var(--tx)">
                    {{ $id->status }}
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="flex flex-col p-3 rounded-lg border" style="background:var(--bg3); border-color:var(--b)">
                <span class="text-[10px] font-bold uppercase opacity-60 mb-1" style="color:var(--tx2)">Solicitante</span>
                <span class="text-xs font-bold" style="color:var(--tx)">{{ $id->user->username ?? '—' }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border" style="background:var(--bg3); border-color:var(--b)">
                <span class="text-[10px] font-bold uppercase opacity-60 mb-1" style="color:var(--tx2)">Fecha Solicitud</span>
                <span class="text-xs font-bold" style="color:var(--tx)">{{ $id->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="flex flex-col p-3 rounded-lg border" style="background:var(--bg3); border-color:var(--b)">
                <span class="text-[10px] font-bold uppercase opacity-60 mb-1" style="color:var(--tx2)">Sede / Ubicación</span>
                <span class="text-xs font-bold" style="color:var(--tx)">{{ $id->facility }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Technical Details Selection --}}
        <div class="p-4 rounded-xl border bg-sigma-bg2" style="border-color:var(--b)">
            <h4 class="text-xs font-black uppercase tracking-widest mb-4 opacity-50" style="color:var(--tx2)">Asignación Técnica</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase mb-1.5 opacity-60" style="color:var(--tx2)">Activo Relacionado</label>
                    @if ($canClose)
                        <select name="asset_id" class="w-full text-xs tomselect" 
                                hx-post="{{ route('maintenance.patch', $id->id) }}" hx-trigger="change" hx-vals='{"field": "asset_id"}'>
                            <option value=""></option>
                            @foreach ($assets as $r)
                                <option value="{{ $r->id }}" {{ ($r->id == $id->asset_id) ? 'selected' : '' }}>
                                    {{ mb_convert_case($r->hostname ?? '', MB_CASE_TITLE, 'UTF-8').' | '.$r->serial.' | '.$r->sap }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="text-xs font-bold p-2 border rounded" style="background:var(--bg); border-color:var(--b)">{{ $id->asset->hostname ?? '—' }}</div>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase mb-1.5 opacity-60" style="color:var(--tx2)">Prioridad de Atención</label>
                    @if ($canClose)
                        <select name="priority" class="w-full text-xs p-2 border rounded bg-sigma-bg shadow-sm" style="border-color:var(--b); color:var(--tx)"
                                hx-post="{{ route('maintenance.patch', $id->id) }}" hx-trigger="change" hx-vals='{"field": "priority"}'>
                            <option value=""></option>
                            <option value="High" {{ $id->priority === 'High' ? 'selected' : '' }}>Alta</option>
                            <option value="Medium" {{ $id->priority === 'Medium' ? 'selected' : '' }}>Media</option>
                            <option value="Low" {{ $id->priority === 'Low' ? 'selected' : '' }}>Baja</option>
                        </select>
                    @else
                        <div class="text-xs font-bold p-2 border rounded" style="background:var(--bg); border-color:var(--b)">{{ $id->priority ?? '—' }}</div>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase mb-1.5 opacity-60" style="color:var(--tx2)">Técnico Asignado</label>
                    @if ($canClose)
                        <select name="assignee_id" class="w-full text-xs tomselect" 
                                hx-post="{{ route('maintenance.patch', $id->id) }}" hx-trigger="change" hx-vals='{"field": "assignee_id"}'>
                            <option value=""></option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ ($u->id == $id->assignee_id) ? 'selected' : '' }}>
                                    {{ $u->username }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="text-xs font-bold p-2 border rounded" style="background:var(--bg); border-color:var(--b)">{{ $id->assignee->username ?? '—' }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Closing Details --}}
        <div class="p-4 rounded-xl border bg-sigma-bg2" style="border-color:var(--b)">
            <h4 class="text-xs font-black uppercase tracking-widest mb-4 opacity-50" style="color:var(--tx2)">Información de Cierre</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase mb-1.5 opacity-60" style="color:var(--tx2)">Código SGC / Documento</label>
                    <input type="text" name="sgc" value="{{ $id->sgc }}" 
                           class="w-full text-xs p-2 border rounded bg-sigma-bg shadow-sm" style="border-color:var(--b); color:var(--tx)"
                           hx-post="{{ route('maintenance.patch', $id->id) }}" hx-trigger="change" hx-vals='{"field": "sgc"}'>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase mb-1.5 opacity-60" style="color:var(--tx2)">Causa Raíz / Diagnóstico</label>
                    <textarea name="root_cause" rows="4"
                              class="w-full text-xs p-2 border rounded bg-sigma-bg shadow-sm" style="border-color:var(--b); color:var(--tx)"
                              hx-post="{{ route('maintenance.patch', $id->id) }}" hx-trigger="change" hx-vals='{"field": "root_cause"}'>{{ $id->root_cause }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if(typeof TomSelect !== 'undefined') {
        document.querySelectorAll('.tomselect').forEach(el => {
            if (!el.tomselect) new TomSelect(el, {
                create: false,
                sortField: {field: "text", direction: "asc"}
            });
        });
    }
</script>
