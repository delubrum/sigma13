<div x-data="{ method: {{ $cause ? $cause->method : 0 }} }">

    @if($cause)
        {{-- View mode --}}
        <div class="p-4 space-y-4">
            <div class="space-y-2">
                <div class="text-[10px] font-black uppercase opacity-50">Causa General</div>
                <div class="p-3 rounded-lg border text-[12px]" style="background:var(--bg2); border-color:var(--b)">
                    {{ $cause->reason }}
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-[10px] font-black uppercase opacity-50">Método: {{ $cause->method === 1 ? '5 Porqués' : 'Archivo' }}</div>
                @if($cause->method === 1 && is_array($cause->whys))
                    @foreach($cause->whys as $i => $why)
                        <div class="p-2 rounded border text-[11px]" style="background:var(--bg2); border-color:var(--b)">
                            <span class="font-bold opacity-50">{{ $i + 1 }}.</span> {{ $why }}
                        </div>
                    @endforeach
                @elseif($cause->method === 2 && $cause->file)
                    <a href="{{ asset('storage/'.$cause->file) }}" target="_blank"
                       class="flex items-center gap-2 text-blue-500 hover:underline text-[12px] font-bold">
                        <i class="ri-file-download-line text-lg"></i> Descargar Archivo
                    </a>
                @endif
            </div>

            <div class="space-y-2">
                <div class="text-[10px] font-black uppercase opacity-50">Causa Probable</div>
                <div class="p-3 rounded-lg border text-[12px]" style="background:var(--bg2); border-color:var(--b)">
                    {{ $cause->probable }}
                </div>
            </div>
        </div>

    @else
        {{-- Create mode --}}
        <form hx-post="{{ route('improvement.causes.upsert') }}"
              hx-target="#modal-body-2"
              hx-swap="innerHTML"
              enctype="multipart/form-data"
              class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="improvement_id" value="{{ $improvementId }}">

            <div>
                <label class="label-sm">Causa General *</label>
                <input type="text" name="reason" required class="input-field w-full" placeholder="Describa la causa raíz...">
            </div>

            <div>
                <label class="label-sm">Método de Análisis *</label>
                <select name="method" x-model.number="method" class="input-field w-full" required>
                    <option value="0">Seleccionar...</option>
                    <option value="1">5 Porqués</option>
                    <option value="2">Archivo</option>
                </select>
            </div>

            {{-- 5 Whys --}}
            <div x-show="method === 1" x-transition class="space-y-2">
                <label class="label-sm">5 Porqués</label>
                @for($i = 0; $i < 5; $i++)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black opacity-40 w-4">{{ $i + 1 }}.</span>
                        <input type="text" name="whys[]" class="input-field flex-1" placeholder="¿Por qué?">
                    </div>
                @endfor
            </div>

            {{-- File upload --}}
            <div x-show="method === 2" x-transition>
                <label class="label-sm">Archivo de Análisis</label>
                <input type="file" name="file" class="input-field w-full" accept=".pdf,.doc,.docx,.xlsx,.jpg,.png">
            </div>

            {{-- Probable cause (visible when method selected) --}}
            <div x-show="method > 0" x-transition>
                <label class="label-sm">Causa Probable *</label>
                <textarea name="probable" rows="3" required class="input-field w-full resize-none"
                          placeholder="Conclusión del análisis..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-sm btn-secondary"
                        onclick="document.getElementById('modal-body-2').innerHTML = ''">
                    Cancelar
                </button>
                <button type="submit" class="btn-sm btn-primary">
                    <i class="ri-save-line mr-1"></i> Guardar Causa
                </button>
            </div>
        </form>
    @endif

</div>
