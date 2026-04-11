<?php
$read_class = 'bg-gray-100 p-2 border border-gray-300 rounded text-gray-800 font-medium w-full';
$base_class = 'w-full border rounded px-3 py-2 transition-all';

// Según tu aclaración: hired es bool (si ya se tomó una decisión)
// y status es el texto ('screening', 'profile', etc.)
$is_hired_interview = (isset($id->hired) && ($id->hired == 1 || $id->hired === true));
$current_status = $id->status ?? '';

$status_options = [
    '' => '',
    'screening' => 'Proceed with Process',
    'profile' => 'Does not meet the profile',
    'lead' => 'Rejected by hiring manager after interview',
    'attitude' => 'Negative attitude during the process',
    'salary' => 'Salary expectations exceed the offer',
    'tests' => 'Unsatisfactory assessment results',
];

$hasDisc = ! empty($id->disc_answers);
$hasPf = ! empty($id->pf_answers);
$assigned = $id->psychometrics ?? ''; // Both, CISD, PF, Other
?>

<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-user-add-line text-xl"></i>
        <span>Interview</span>
    </h2>

    <div class="p-4 space-y-8">

        <?php /* ── SECCIÓN: Pruebas psicométricas ── */ ?>
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2 text-sm uppercase tracking-wider font-bold">Estado de Evaluaciones</h2>
            
            <?php if ($assigned === 'Other') { ?>
                <div class="p-8 border-2 border-dashed rounded-xl bg-blue-50 border-blue-200 text-blue-800 flex flex-col items-center justify-center text-center">
                    <i class="ri-external-link-line text-4xl mb-2 opacity-70 text-blue-500"></i>
                    <p class="font-bold text-base uppercase tracking-tight">Pruebas Externas Asignadas</p>
                    <p class="text-sm">Se han asignado otras pruebas para este candidato por fuera del sistema.</p>
                </div>
            <?php } elseif (in_array($assigned, ['Both', 'CISD', 'PF'])) { ?>
                <div class="grid <?= ($assigned === 'Both') ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1' ?> gap-4">
                    
                    <?php if ($assigned === 'Both' || $assigned === 'CISD') { ?>
                        <div class="flex flex-col">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-tighter">Prueba CISD (DISC)</label>
                            <?php if ($hasDisc) { ?>
                                <div class="border rounded-xl bg-white shadow-sm overflow-hidden" hx-get="?c=Recruitment&a=DiscResult&id=<?= $id->id ?>" hx-trigger="load" hx-target="this" hx-swap="innerHTML">
                                    <div class="p-6 flex items-center justify-center text-gray-400 gap-2 text-sm"><i class="ri-loader-4-line animate-spin text-blue-500"></i> Cargando DISC...</div>
                                </div>
                            <?php } else { ?>
                                <div class="p-6 border-2 border-dashed rounded-xl bg-gray-50 flex flex-col items-center justify-center text-center">
                                    <i class="ri-time-line text-orange-400 text-2xl mb-1 opacity-70"></i>
                                    <p class="text-sm font-semibold text-gray-600 uppercase">Pendiente</p>
                                    <p class="text-[10px] text-gray-400 mt-1 italic uppercase tracking-tighter">El candidato no ha realizado la prueba DISC asignada</p>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($assigned === 'Both' || $assigned === 'PF') { ?>
                        <div class="flex flex-col">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 ml-1 tracking-tighter">Prueba PF (Personalidad)</label>
                            <?php if ($hasPf) { ?>
                                <div class="border rounded-xl bg-white shadow-sm overflow-hidden" hx-get="?c=Recruitment&a=PFResult&id=<?= $id->id ?>" hx-trigger="load" hx-target="this" hx-swap="innerHTML">
                                    <div class="p-6 flex items-center justify-center text-gray-400 gap-2 text-sm"><i class="ri-loader-4-line animate-spin text-purple-500"></i> Cargando PF...</div>
                                </div>
                            <?php } else { ?>
                                <div class="p-6 border-2 border-dashed rounded-xl bg-gray-50 flex flex-col items-center justify-center text-center">
                                    <i class="ri-time-line text-orange-400 text-2xl mb-1 opacity-70"></i>
                                    <p class="text-sm font-semibold text-gray-600 uppercase">Pendiente</p>
                                    <p class="text-[10px] text-gray-400 mt-1 italic uppercase tracking-tighter">El candidato no ha realizado la prueba PF asignada</p>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                </div>
            <?php } else { ?>
                <div class="p-4 bg-gray-50 border-2 border-dashed rounded-xl text-gray-500 text-sm text-center italic">
                    No se han asignado pruebas psicométricas para este proceso.
                </div>
            <?php } ?>
        </section>

        <?php /* ── SECCIÓN: Concepto de Selección ── */ ?>
        <section>
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h2 class="text-xl font-semibold text-gray-700">Concepto de Selección</h2>
                <?php if (! $is_hired_interview) { ?>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="aiLoading()" hx-post="?c=Recruitment&a=GenerateAIConcept" hx-vals='{"id":<?= $id->id ?>,"promptVersion":"1"}' hx-target="#ai-response-container" hx-swap="innerHTML" class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white hover:bg-gray-700 flex items-center gap-1.5"><i class="ri-sparkling-2-line text-xs"></i> Sucinto</button>
                    <button type="button" onclick="aiLoading()" hx-post="?c=Recruitment&a=GenerateAIConcept" hx-vals='{"id":<?= $id->id ?>,"promptVersion":"2"}' hx-target="#ai-response-container" hx-swap="innerHTML" class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white hover:bg-gray-700 flex items-center gap-1.5"><i class="ri-sparkling-2-line text-xs"></i> Detallado</button>
                    <button type="button" onclick="aiLoading()" hx-post="?c=Recruitment&a=GenerateAIConcept" hx-vals='{"id":<?= $id->id ?>,"promptVersion":"3"}' hx-target="#ai-response-container" hx-swap="innerHTML" class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white hover:bg-gray-700 flex items-center gap-1.5"><i class="ri-sparkling-2-line text-xs"></i> Psicológico</button>
                </div>
                <?php } ?>
            </div>

            <div class="space-y-4">
                <div id="ai-response-container"></div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600">Observaciones o comentarios del entrevistador</label>
                    <?php if ($is_hired_interview) { ?>
                        <div class="<?= $read_class ?> whitespace-pre-wrap"><?= htmlspecialchars($id->concept ?? '') ?></div>
                    <?php } else { ?>
                        <textarea id="obs" name="concept" rows="4"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"concept"}'
                            hx-target="this"
                            placeholder="Escribe las observaciones aquí..."
                            class="<?= $base_class ?>"><?= htmlspecialchars($id->concept ?? '') ?></textarea>
                    <?php } ?>
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-600">Estado del candidato</label>
                    <?php if ($is_hired_interview) { ?>
                        <?php
                            // Ahora usamos 'status' para obtener el texto del label
                            $label = $status_options[$current_status] ?? $current_status;
                        // Verificamos si es screening para el color verde
                        $is_proceed = ($current_status === 'screening');
                        ?>
                        <div class="p-3 border-2 rounded-xl flex items-center gap-3 shadow-sm <?= $is_proceed ? 'bg-green-50 border-green-400 text-green-800' : 'bg-red-50 border-red-400 text-red-800' ?>">
                            <div class="<?= $is_proceed ? 'bg-green-500' : 'bg-red-500' ?> text-white rounded-full p-1 flex items-center justify-center">
                                <i class="<?= $is_proceed ? 'ri-checkbox-circle-line' : 'ri-close-circle-line' ?> text-xl font-bold"></i>
                            </div>
                            <span class="font-bold text-lg uppercase tracking-tight">
                                <?= htmlspecialchars($label) ?>
                            </span>
                        </div>
                    <?php } else { ?>
                        <select id="hired_select" name="hired"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change[this.value != '']"
                            hx-vals='{"id":<?= $id->id ?>,"field":"hired"}'
                            data-last-value="<?= $id->hired ?? '' ?>"
                            onfocus="this.setAttribute('data-last-value', this.value)"
                            hx-on:htmx:before-request="
                                const obs = document.getElementById('obs').value.trim();
                                if(!obs){ 
                                    alert('Debe ingresar las observaciones antes de cambiar el estado.'); 
                                    event.preventDefault(); 
                                    this.value = this.getAttribute('data-last-value');
                                    document.getElementById('obs').focus();
                                }
                            "
                            class="<?= $base_class ?> focus:ring-2 focus:ring-black outline-none">
                            <?php foreach ($status_options as $val => $label) { ?>
                                <option value="<?= $val ?>" <?= ($id->status ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>
</div>

<input type="hidden" name="candidate_id" value="<?= htmlspecialchars($id->id) ?>">

<script>
    function aiLoading() {
        document.getElementById('ai-response-container').innerHTML =
            '<div class="p-4 flex items-center gap-2 text-gray-400 text-sm italic border rounded-xl">' +
            '<i class="ri-loader-2-line animate-spin mr-1 text-black"></i> Analizando candidato...</div>';
    }

    htmx.on('cvChanged', function () {
        const cinfo = document.querySelector('#cinfo');
        if (cinfo) htmx.trigger(cinfo, 'refresh');
    });
</script>