<form hx-post="{{ route('improvement.activities.upsert') }}"
      hx-target="#modal-body-2"
      hx-swap="innerHTML"
      enctype="multipart/form-data"
      class="p-4 space-y-4"
      x-data="{ fulfill: false }">
    @csrf
    <input type="hidden" name="improvement_id" value="{{ $improvementId }}">
    <input type="hidden" name="activity_id"    value="{{ $activity->id }}">
    <input type="hidden" name="is_close"       value="1">
    <input type="hidden" name="fulfill" :value="fulfill ? '1' : '0'">

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label-sm">Fecha del resultado *</label>
            <input type="text" name="done_date" data-widget="flatpickr" required class="input-field w-full">
        </div>
        <div>
            <label class="label-sm">¿Actividad completada?</label>
            <select x-model="fulfill" class="input-field w-full">
                <option value="false">No</option>
                <option value="true">Sí</option>
            </select>
        </div>
    </div>

    <div>
        <label class="label-sm">Resultados *</label>
        <textarea name="results" rows="4" required class="input-field w-full resize-none"
                  placeholder="Describa los resultados obtenidos..."></textarea>
    </div>

    <div>
        <label class="label-sm">Archivo evidencia (opcional)</label>
        <input type="file" name="file" class="input-field w-full" accept="image/*,.pdf">
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-sm btn-secondary"
                onclick="document.getElementById('modal-body-2').innerHTML = ''">
            Cancelar
        </button>
        <button type="submit" class="btn-sm btn-primary">
            <i class="ri-save-line mr-1"></i> Registrar Resultado
        </button>
    </div>
</form>
