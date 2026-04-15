<form hx-post="{{ route('recruitment.candidates.upsert') }}"
      hx-target="#modal-body-2"
      hx-swap="innerHTML"
      class="p-4 space-y-3">
    @csrf
    <input type="hidden" name="recruitment_id" value="{{ $recruitmentId }}">
    @if($candidate)
        <input type="hidden" name="id" value="{{ $candidate->id }}">
    @endif

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="label-sm">Tipo *</label>
            <select name="kind" required class="input-field w-full">
                <option value="">Seleccionar...</option>
                @foreach(['Externo'=>'Externo','Interno'=>'Interno','Referido'=>'Referido'] as $v=>$l)
                    <option value="{{ $v }}" @selected(($candidate->kind ?? '') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label-sm">Nombre Completo *</label>
            <input type="text" name="name" required class="input-field w-full"
                   value="{{ $candidate->name ?? '' }}" placeholder="Nombre y apellidos">
        </div>
        <div>
            <label class="label-sm">Cédula (CC) *</label>
            <input type="text" name="cc" required class="input-field w-full"
                   value="{{ $candidate->cc ?? '' }}">
        </div>
        <div>
            <label class="label-sm">Email *</label>
            <input type="email" name="email" required class="input-field w-full"
                   value="{{ $candidate->email ?? '' }}">
        </div>
        <div>
            <label class="label-sm">Teléfono</label>
            <input type="text" name="phone" class="input-field w-full"
                   value="{{ $candidate->phone ?? '' }}">
        </div>
        <div>
            <label class="label-sm">Fuente CV</label>
            <select name="cv_source" class="input-field w-full">
                <option value="">Seleccionar...</option>
                @foreach(['LinkedIn','Computrabajo','Referido','Directo','Otro'] as $s)
                    <option value="{{ $s }}" @selected(($candidate->cv_source ?? '') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label-sm">Psicométricas</label>
            <select name="psychometrics" class="input-field w-full">
                <option value="None">Ninguna</option>
                <option value="CISD" @selected(($candidate->psychometrics ?? '') === 'CISD')>DISC</option>
                <option value="PF" @selected(($candidate->psychometrics ?? '') === 'PF')>16PF</option>
                <option value="Both" @selected(($candidate->psychometrics ?? '') === 'Both')>Ambas</option>
            </select>
        </div>
        <div>
            <label class="label-sm">Reclutador</label>
            <select name="recruiter_id" class="input-field w-full" data-widget="slimselect">
                <option value="">Sin asignar</option>
            </select>
        </div>
        <div>
            <label class="label-sm">Fecha Cita</label>
            <input type="text" name="appointment" data-widget="flatpickr" class="input-field w-full"
                   value="{{ $candidate->appointment ?? '' }}">
        </div>
        <div>
            <label class="label-sm">Modalidad</label>
            <select name="appointment_mode" class="input-field w-full">
                <option value="">Seleccionar...</option>
                <option value="Presencial" @selected(($candidate->appointment_mode ?? '') === 'Presencial')>Presencial</option>
                <option value="Virtual" @selected(($candidate->appointment_mode ?? '') === 'Virtual')>Virtual</option>
            </select>
        </div>
        <div>
            <label class="label-sm">Sede</label>
            <select name="appointment_location" class="input-field w-full">
                <option value="">Seleccionar...</option>
                <option value="ESM1" @selected(($candidate->appointment_location ?? '') === 'ESM1')>ESM1</option>
                <option value="ESM2" @selected(($candidate->appointment_location ?? '') === 'ESM2')>ESM2</option>
            </select>
        </div>
        <div>
            <label class="label-sm">Link Teams</label>
            <input type="text" name="teams_link" class="input-field w-full"
                   value="{{ $candidate->teams_link ?? '' }}" placeholder="https://teams.microsoft.com/...">
        </div>
    </div>

    <div>
        <label class="label-sm">Instrucciones Adicionales</label>
        <textarea name="additional_instructions" rows="2" class="input-field w-full resize-none"
                  placeholder="Indicaciones para el candidato...">{{ $candidate->additional_instructions ?? '' }}</textarea>
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-sm btn-secondary"
                onclick="document.getElementById('modal-body-2').innerHTML=''">Cancelar</button>
        <button type="submit" class="btn-sm btn-primary">
            <i class="ri-save-line mr-1"></i> {{ $candidate ? 'Actualizar' : 'Registrar' }}
        </button>
    </div>
</form>
