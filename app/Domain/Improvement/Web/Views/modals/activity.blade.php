<form hx-post="{{ route('improvement.activities.upsert') }}"
      hx-target="#modal-body-2"
      hx-swap="innerHTML"
      class="p-4 space-y-4">
    @csrf
    <input type="hidden" name="improvement_id" value="{{ $improvementId }}">
    @if($activity) <input type="hidden" name="id" value="{{ $activity->id }}"> @endif
    <input type="hidden" name="is_close" value="0">

    <div>
        <label class="label-sm">Actividad / Acción *</label>
        <textarea name="action" rows="3" required class="input-field w-full resize-none"
                  placeholder="¿Qué se va a hacer?">{{ $activity?->action }}</textarea>
    </div>

    <div>
        <label class="label-sm">Cómo realizarlo *</label>
        <textarea name="how_to" rows="3" required class="input-field w-full resize-none"
                  placeholder="¿Cómo se va a ejecutar?">{{ $activity?->how_to }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label-sm">Responsable</label>
            <select name="responsible_id" data-widget="slimselect" class="w-full">
                <option value="">Seleccionar...</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $activity?->responsible_id == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label-sm">Fecha límite</label>
            <input type="text" name="whenn" data-widget="flatpickr" class="input-field w-full"
                   value="{{ $activity?->whenn?->format('Y-m-d') }}">
        </div>
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-sm btn-secondary"
                onclick="document.getElementById('modal-body-2').innerHTML = ''">
            Cancelar
        </button>
        <button type="submit" class="btn-sm btn-primary">
            <i class="ri-save-line mr-1"></i> {{ $activity ? 'Actualizar' : 'Crear Actividad' }}
        </button>
    </div>
</form>
