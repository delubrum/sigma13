<form hx-post="{{ route('recruitment.assign.save', $recruitment->id) }}"
      hx-target="#modal-body-2"
      hx-swap="innerHTML"
      class="p-4 space-y-4">
    @csrf
    <div>
        <label class="label-sm">Reclutador / Asignado</label>
        <select name="assignee_id" required class="input-field w-full" data-widget="slimselect">
            <option value="">Seleccionar...</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected($recruitment->assignee_id == $user->id)>{{ $user->username }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-sm btn-secondary"
                onclick="document.getElementById('modal-body-2').innerHTML=''">Cancelar</button>
        <button type="submit" class="btn-sm btn-primary">
            <i class="ri-save-line mr-1"></i> Guardar
        </button>
    </div>
</form>
