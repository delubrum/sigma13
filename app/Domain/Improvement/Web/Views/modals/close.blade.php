<form hx-post="{{ route('improvement.close.save', $improvement->id) }}"
      hx-target="#modal-body-2"
      hx-swap="innerHTML"
      class="p-4 space-y-4">
    @csrf

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label-sm">Fecha de cierre *</label>
            <input type="text" name="cdate" data-widget="flatpickr" required class="input-field w-full">
        </div>
        <div>
            <label class="label-sm">Conveniencia *</label>
            <select name="convenience" required class="input-field w-full">
                <option value="">Seleccionar...</option>
                <option value="Conforme">Conforme</option>
                <option value="No Conforme">No Conforme</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label-sm">Adecuación *</label>
            <select name="adequacy" required class="input-field w-full">
                <option value="">Seleccionar...</option>
                <option value="Conforme">Conforme</option>
                <option value="No Conforme">No Conforme</option>
            </select>
        </div>
        <div>
            <label class="label-sm">Eficacia *</label>
            <select name="effectiveness" required class="input-field w-full">
                <option value="">Seleccionar...</option>
                <option value="Conforme">Conforme</option>
                <option value="No Conforme">No Conforme</option>
            </select>
        </div>
    </div>

    <div>
        <label class="label-sm">Notas adicionales</label>
        <textarea name="notes" rows="3" class="input-field w-full resize-none"
                  placeholder="Observaciones finales..."></textarea>
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-sm btn-secondary"
                onclick="document.getElementById('modal-body-2').innerHTML = ''">
            Cancelar
        </button>
        <button type="submit" class="btn-sm btn-primary">
            <i class="ri-checkbox-circle-line mr-1"></i> Confirmar Cierre
        </button>
    </div>
</form>
