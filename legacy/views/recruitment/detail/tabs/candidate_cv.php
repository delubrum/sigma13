<?php
// Lógica para determinar si el formulario debe ser de solo lectura.
$is_hired = false;

// Estilos base
$read_class = 'bg-gray-100 p-2 border border-gray-300 rounded text-gray-800 font-medium w-full min-h-[42px] flex items-center';
$base_class = 'w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrevista de Reclutamiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://unpkg.com/htmx.org@1.9.6" integrity="sha384-FhXw7b6AlE/jyjlZH5iHa/tTe9EpJ1Y55RjcgPbjeWMskSxZt1v9qkxLJWNJaGni" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>
<body class="bg-gray-50 py-10">

    <div id="loading" class="htmx-indicator pointer-events-none absolute z-[80] h-full w-full top-0 left-0 align-middle bg-gray-50">
        <div class="h-full w-full flex flex-col justify-center place-items-center my-auto">
            <div class="w-24 h-24 bg-no-repeat bg-center bg-[url('app/assets/img/loader.gif')] bg-contain opacity-90"></div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden">
        
        <div class="bg-gray-900 px-8 py-6 flex justify-between items-center text-white">
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <i class="ri-user-add-line text-blue-400"></i>
                Proceso de Entrevista
            </h1>
            <?php if ($is_hired) { ?>
                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Perfil Contratado
                </span>
            <?php } ?>
        </div>

        <form id="cvForm" class="space-y-10 p-4">


            <section class="bg-blue-50 p-5 rounded-xl border border-blue-200 mb-10 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex items-center h-6">
                        <?php
                            // Verificamos el estado actual en la base de datos
                            $is_checked = (isset($id->data_consent) && ($id->data_consent == '1' || $id->data_consent == true));
?>
                        
                        <input id="data_consent" 
                            name="data_consent" 
                            type="checkbox" 
                            value="1"
                            <?= $is_checked ? 'checked' : '' ?>
                            <?php if ($is_hired) { ?>
                                disabled
                                class="w-5 h-5 text-gray-400 border-gray-300 rounded cursor-not-allowed opacity-70"
                            <?php } else { ?>
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-vals='js:{"id": <?= (int) $id->id ?>, "field": "data_consent", "value": event.target.checked ? 1 : 0}'
                                hx-trigger="change"
                                hx-target="this"
                                hx-indicator="#loading"
                                class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer transition-all"
                            <?php } ?>>
                    </div>

                    <div class="flex-1 text-sm">
                        <label for="data_consent" class="block font-bold text-gray-800 text-base mb-1 <?= $is_hired ? 'cursor-not-allowed' : 'cursor-pointer' ?>">
                            Autorización para el Tratamiento de Datos Personales
                        </label>
                        <p class="text-gray-600 leading-relaxed">
                            En cumplimiento de la Ley 1581 de 2012, autorizo de manera previa, expresa e informada a la compañía para recolectar, almacenar y usar mis datos personales conforme a la 
                            <a href="https://sigma.es-metals.com/sigma/uploads/pc/adp.pdf" target="_blank" class="text-blue-600 font-semibold underline hover:text-blue-800 inline-flex items-center gap-1">
                                Política de Tratamiento de Información <i class="ri-external-link-line text-xs"></i>
                            </a>.
                        </p>

                        <?php if ($is_hired) { ?>
                            <div class="mt-2 inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs font-medium">
                                <i class="ri-lock-line mr-1"></i> Campo bloqueado (Perfil Contratado)
                            </div>
                            <?php if (! $is_checked) { ?>
                                <p class="text-red-600 font-bold mt-1 text-xs uppercase tracking-tight">
                                    <i class="ri-error-warning-line"></i> Este candidato no aceptó los términos originalmente.
                                </p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">1. Información Básica</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Nombre completo</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->name ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="name" 
                                value="<?= $id->name ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"name"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>" required>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Cédula</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->cc ?? '' ?></div>
                        <?php } else { ?>
                            <input type="number" name="cc" 
                                value="<?= $id->cc ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"cc"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>" required>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Correo electrónico</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->email ?? '' ?></div>
                        <?php } else { ?>
                            <input type="email" name="email" 
                                value="<?= $id->email ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"email"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Celular</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->phone ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="phone" 
                                value="<?= $id->phone ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"phone"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Edad</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->age ?? '' ?></div>
                        <?php } else { ?>
                            <input type="number" name="age" 
                                value="<?= $id->age ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"age"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Ciudad de residencia</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->city ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="city" 
                                value="<?= $id->city ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"city"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Barrio de residencia</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->neighborhood ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="neighborhood" 
                                value="<?= $id->neighborhood ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"neighborhood"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">2. Información Familiar</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Estado civil</label>
                        <?php if ($is_hired) { ?>
                            <?php
        $maritalstatus_map = [
            'Soltero(a)' => 'Soltero(a)',
            'Casado(a)' => 'Casado(a)',
            'Unión libre' => 'Unión libre',
            'Divorciado(a)' => 'Divorciado(a)',
            'Viudo(a)' => 'Viudo(a)',
        ];
                            ?>
                            <div class="truncate <?= $read_class ?>">
                                <?= $maritalstatus_map[$id->maritalstatus ?? ''] ?? '' ?>
                            </div>
                        <?php } else { ?>
                            <select name="maritalstatus" 
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"maritalstatus"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="<?= $base_class ?>">
                                <option value=""></option>
                                <option value="Soltero(a)" <?= ($id->maritalstatus ?? '') == 'Soltero(a)' ? 'selected' : '' ?>>Soltero(a)</option>
                                <option value="Casado(a)" <?= ($id->maritalstatus ?? '') == 'Casado(a)' ? 'selected' : '' ?>>Casado(a)</option>
                                <option value="Unión libre" <?= ($id->maritalstatus ?? '') == 'Unión libre' ? 'selected' : '' ?>>Unión libre</option>
                                <option value="Divorciado(a)" <?= ($id->maritalstatus ?? '') == 'Divorciado(a)' ? 'selected' : '' ?>>Divorciado(a)</option>
                                <option value="Viudo(a)" <?= ($id->maritalstatus ?? '') == 'Viudo(a)' ? 'selected' : '' ?>>Viudo(a)</option>
                            </select>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Con quién convive</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->liveswith ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="liveswith" 
                                value="<?= $id->liveswith ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"liveswith"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>
                </div>

                <div class="mt-4 space-y-2">

                    <div class="flex items-center space-x-4">
                        <span class="font-medium text-gray-700">¿Tiene relación de consanguinidad, afinidad, sentimental con algún empleado, directivo, administrador, accionista, empresa de la competencia, cliente o proveedor en C.I. GRUPO TECNOGLASS? GRUPO TECNOGLASS: TECNOGLASS INC., TECNOGLASS S.A.S., ENERGÍA SOLAR, etc.</span>
                        <?php if ($is_hired) { ?>
                            <span class="text-gray-800 font-bold ml-4">
                                <?= ($id->relativework ?? '') == '1' ? 'Sí' : 'No' ?>
                            </span>
                        <?php } else { ?>
                            <!-- Contenedor con hx-vals para enviar solo id+field + el valor seleccionado -->
                            <div class="flex items-center space-x-4" 
                                hx-post="?c=Recruitment&a=UpdateField" 
                                hx-indicator="#loading" 
                                hx-trigger="change" 
                                hx-target="this"
                                hx-vals='{"id":<?= $id->id ?>,"field":"relativework"}'>
                                <label>
                                    <input type="radio" name="relativework" value="1" class="ml-2" <?= ($id->relativework ?? '') == '1' ? 'checked' : '' ?>> Sí
                                </label>
                                <label>
                                    <input type="radio" name="relativework" value="0" class="ml-2" <?= ($id->relativework ?? '') == '0' ? 'checked' : '' ?>> No
                                </label>
                            </div>
                            <?php } ?>
                    </div>

                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium mb-2">
                        En caso afirmativo, favor diligencie la siguiente información:
                    </label>

                    <?php if ($is_hired) { ?>
                        <?php
                            // Verificación segura para evitar el error Deprecated
                            $raw_data = $id->relatives_data ?? '';
                        $relatives_data = ! empty($raw_data) ? json_decode($raw_data, true) : [];
                        if (! is_array($relatives_data)) {
                            $relatives_data = [];
                        }
                        ?>
                        
                        <?php if (! empty($relatives_data)) { ?>
                            <?php foreach ($relatives_data as $relative) { ?>
                                <div class="grid md:grid-cols-4 gap-3 items-center mb-2 p-2 border rounded bg-white shadow-sm">
                                    <div class="truncate font-semibold"><?= htmlspecialchars($relative['fullname'] ?? 'N/A') ?></div>
                                    <div class="truncate text-sm text-gray-600">Cargo: <?= htmlspecialchars($relative['job_position'] ?? 'N/A') ?></div>
                                    <div class="truncate text-sm text-gray-600">Empresa: <?= htmlspecialchars($relative['company'] ?? 'N/A') ?></div>
                                    <div class="truncate text-sm text-gray-600">
                                        Labora actualmente: 
                                        <span class="font-bold"><?= ($relative['currently'] ?? '0') == '1' ? 'Sí' : 'No' ?></span>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-sm text-gray-500 italic">No se registró información de familiares.</div>
                        <?php } ?>

                    <?php } else { ?>

                        <?php
                        // 1. Cargar lo que trae DB de forma segura
                        $raw = $id->relatives_data ?? [];

                        // 2. Si viene como string, decodificarlo solo si no es nulo
                        if (is_string($raw)) {
                            $raw = ! empty($raw) ? json_decode($raw, true) : [];
                        }

                        // Asegurar que siempre sea un array antes de encodear para Alpine
                        if (! is_array($raw)) {
                            $raw = [];
                        }

                        // 3. Convertir a JSON limpio para Alpine
                        $relatives_json = json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        ?>

                        <div x-data='{
                                relatives: <?= $relatives_json ?>,
                                addRelative() {
                                    this.relatives.push({ fullname: "", job_position: "", company: "", currently: "" });
                                    this.sync();
                                },
                                removeRelative(i) {
                                    this.relatives.splice(i, 1);
                                    this.sync();
                                },
                                sync() {
                                    this.$nextTick(() => {
                                        let payload = JSON.stringify(this.relatives);
                                        htmx.ajax("POST", "?c=Recruitment&a=UpdateField", {
                                            target: "#dummy",
                                            values: {
                                                id: <?= (int) $id->id ?>,
                                                field: "relatives_data",
                                                relatives_data: payload
                                            }
                                        });
                                    });
                                }
                            }'
                            class="space-y-2"
                        >

                            <div id="dummy" style="display:none;"></div>

                            <template x-for="(relative, index) in relatives" :key="index">
                                <div class="grid md:grid-cols-4 gap-3 items-center mb-2">

                                    <input type="text" x-model="relative.fullname"
                                        @change="sync()"
                                        placeholder="Nombre completo"
                                        class="<?= $base_class ?>">

                                    <input type="text" x-model="relative.job_position"
                                        @change="sync()"
                                        placeholder="Cargo"
                                        class="<?= $base_class ?>">

                                    <input type="text" x-model="relative.company"
                                        @change="sync()"
                                        placeholder="Empresa"
                                        class="<?= $base_class ?>">

                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">¿Labora actualmente?</span>

                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                :name="`relatives_${index}_currently`"
                                                value="1"
                                                x-model="relative.currently"
                                                @change="sync()"
                                                class="mr-1"> Sí
                                        </label>

                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                :name="`relatives_${index}_currently`"
                                                value="0"
                                                x-model="relative.currently"
                                                @change="sync()"
                                                class="mr-1"> No
                                        </label>

                                        <button type="button"
                                            @click="removeRelative(index)"
                                            class="text-red-500 hover:text-red-700 text-xl leading-none ml-2">&times;
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addRelative()" 
                                class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                (+) Agregar familiar
                            </button>

                        </div>

                    <?php } ?>
                </div>

            </section>


            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">3. Información Académica</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Último nivel de estudio</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->educationlevel ?? '' ?></div>
                        <?php } else { ?>
                            <select name="educationlevel" 
                                    hx-post="?c=Recruitment&a=UpdateField"
                                    hx-trigger="change"
                                    hx-vals='{"id":<?= $id->id ?>,"field":"educationlevel"}'
                                    hx-target="this"
                                    hx-indicator="#loading"
                                    class="<?= $base_class ?>">
                                <option value="" <?= ($id->educationlevel ?? '') == '' ? 'selected' : '' ?>></option>
                                <option value="Bachillerato" <?= ($id->educationlevel ?? '') == 'Bachillerato' ? 'selected' : '' ?>>Bachillerato</option>
                                <option value="Técnica o Tecnológica" <?= ($id->educationlevel ?? '') == 'Técnica o Tecnológica' ? 'selected' : '' ?>>Técnica o Tecnológica</option>
                                <option value="Profesional" <?= ($id->educationlevel ?? '') == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                                <option value="Postgrado" <?= ($id->educationlevel ?? '') == 'Postgrado' ? 'selected' : '' ?>>Postgrado</option>
                                <option value="Otro" <?= ($id->educationlevel ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Título</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->degree ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="degree" 
                                value="<?= $id->degree ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"degree"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-1 font-medium text-gray-600">Institución</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->school ?? '' ?></div>
                        <?php } else { ?>
                            <input type="text" name="school" 
                                value="<?= $id->school ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"school"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>
                </div>
            </section>

            <?php
            $json_db = $id->work_experience ?? '';
$experiences = json_decode($json_db, true);

if (! is_array($experiences) || empty($experiences)) {
    $experiences = [[
        'company' => $id->company ?? '', 'job_position' => $id->job_position ?? '',
        'reason' => $id->reason ?? '', 'salary' => $id->salary ?? '',
        'duration' => $id->duration ?? '', 'functions' => $id->functions ?? '',
    ]];
}
?>

            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">4. Experiencia Laboral</h2>
                
                <div id="experience-container">
                    <?php foreach ($experiences as $index => $exp) { ?>
                        <div class="experience-block border-b border-gray-100 pb-6 mb-6 last:border-0 relative" id="exp-<?= $index ?>">
                            
                            <?php if (! $is_hired && $index > 0) { ?>
                                <button type="button" 
                                    hx-post="?c=Recruitment&a=RemoveExperienceRow" 
                                    hx-vals='{"id":<?= $id->id ?>, "index":<?= $index ?>}'
                                    hx-target="closest .experience-block" hx-swap="delete"
                                    hx-confirm="¿Quitar esta experiencia?"
                                    class="absolute top-0 right-0 text-red-500 hover:text-red-700 flex items-center text-xs font-bold uppercase">
                                    <i class="ri-delete-bin-line mr-1 text-base"></i> Quitar
                                </button>
                            <?php } ?>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 font-medium text-gray-600">Empresa donde laboró</label>
                                    <input type="text" name="company" value="<?= $exp['company'] ?? '' ?>"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"company"}'
                                        hx-trigger="change delay:500ms" hx-indicator="#loading" class="<?= $base_class ?>">
                                </div>

                                <div>
                                    <label class="block mb-1 font-medium text-gray-600">Cargo</label>
                                    <input type="text" name="job_position" value="<?= $exp['job_position'] ?? '' ?>"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"job_position"}'
                                        hx-trigger="change delay:500ms" hx-indicator="#loading" class="<?= $base_class ?>">
                                </div>

                                <div>
                                    <label class="block mb-1 font-medium text-gray-600">Motivo de terminación</label>
                                    <input type="text" name="reason" value="<?= $exp['reason'] ?? '' ?>"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"reason"}'
                                        hx-trigger="change delay:500ms" hx-indicator="#loading" class="<?= $base_class ?>">
                                </div>

                                <div>
                                    <label class="block mb-1 font-medium text-gray-600">Salario</label>
                                    <input type="number" name="salary" value="<?= $exp['salary'] ?? '' ?>"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"salary"}'
                                        hx-trigger="change delay:500ms" hx-indicator="#loading" class="<?= $base_class ?>">
                                </div>

                                <div>
                                    <label class="block mb-1 font-medium text-gray-600">Tiempo en el cargo</label>
                                    <input type="text" name="duration" value="<?= $exp['duration'] ?? '' ?>"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"duration"}'
                                        hx-trigger="change delay:500ms" hx-indicator="#loading" class="<?= $base_class ?>">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-1 font-medium text-gray-600">Funciones Realizadas</label>
                                    <textarea rows="2" name="functions"
                                        hx-post="?c=Recruitment&a=UpdateField"
                                        hx-vals='{"id":<?= $id->id ?>,"field":"work_experience","index":<?= $index ?>,"subfield":"functions"}'
                                        hx-trigger="change delay:800ms" hx-indicator="#loading" class="<?= $base_class ?> w-full"><?= $exp['functions'] ?? '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php if (! $is_hired) { ?>
                    <button type="button" 
                        hx-get="?c=Recruitment&a=AddExperienceRow&id=<?= $id->id ?>" 
                        hx-target="#experience-container" hx-swap="beforeend"
                        class="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center mb-6">
                        <i class="ri-add-circle-line mr-1 text-lg"></i> Añadir otra experiencia laboral
                    </button>
                <?php } ?>
            </section>

                            <div>
                    <label class="block mb-1 font-medium text-gray-600">Aspiración salarial</label>
                    <?php if ($is_hired) { ?>
                        <div class="truncate <?= $read_class ?>"><?= $id->wage ?? '' ?></div>
                    <?php } else { ?>
                        <input name="wage" 
                            value="<?= $id->wage ?? '' ?>"
                            hx-post="?c=Recruitment&a=UpdateField"
                            hx-trigger="change delay:500ms"
                            hx-vals='{"id":<?= $id->id ?>,"field":"wage"}'
                            hx-target="this"
                            hx-indicator="#loading"
                            class="<?= $base_class ?>">
                    <?php } ?>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block mb-1 font-medium text-gray-600">
                        * ¿Tiene conocimientos en?

                    <?php
        // 1. Obtener el profile_id de forma segura
        $recruitment_data = $this->model->get('profile_id', 'recruitment', 'and id = '.(int) $id->recruitment_id);
$profile_id = $recruitment_data->profile_id ?? null;

$formation = [];
if ($profile_id) {
    // 2. Obtener el item de formación
    $item_data = $this->model->get('content', 'job_profile_items', "and jp_id = $profile_id and kind = 'Formación'");

    // 3. Validar que $item_data sea un objeto y tenga la propiedad data antes de decodificar
    if ($item_data && isset($item_data->content) && ! empty($item_data->content)) {
        $formation = json_decode($item_data->content, true);
        // Asegurar que el resultado sea un array
        if (! is_array($formation)) {
            $formation = [];
        }
    }
}

if (! empty($formation)) { ?>
                        <?php foreach ($formation as $row) { ?>
                            <?php if (! empty(trim($row[0] ?? ''))) { ?>
                                <div class="flex text-xs mb-1 items-start">
                                    <i class="ri-checkbox-circle-line text-blue-500 mr-1 mt-[2px]"></i>
                                    <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[0]) ?></div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    </label>

                    <?php if ($is_hired) { ?>
                        <div class="text-gray-800 font-bold ml-4 mt-2">
                            <?= ($id->has_knowledge ?? '') == '1' ? 'Sí' : 'No' ?>
                        </div>
                    <?php } else { ?>
                        <div class="flex items-center space-x-6 mt-2" 
                            hx-post="?c=Recruitment&a=UpdateField" 
                            hx-indicator="#loading" 
                            hx-trigger="change" 
                            hx-target="this"
                            hx-vals='{"id":<?= (int) $id->id ?>,"field":"has_knowledge"}'>
                            
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                        name="has_knowledge" 
                                        value="1" 
                                        required
                                        <?= ($id->has_knowledge ?? '') == '1' ? 'checked' : '' ?>
                                        class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Sí</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="radio" 
                                        name="has_knowledge" 
                                        value="0" 
                                        required
                                        <?= ($id->has_knowledge ?? '') == '0' ? 'checked' : '' ?>
                                        class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    <?php } ?>
                </div>

            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">5. Inmersión en el Ser</h2>
                <div class="space-y-4">
                    
                    <div>
                        <label class="block mb-1 font-medium text-gray-600">¿Cuáles son sus metas a corto plazo?</label>
                        <?php if ($is_hired) { ?>
                            <div class="<?= $read_class ?> whitespace-pre-wrap"><?= $id->shortgoals ?? '' ?></div>
                        <?php } else { ?>
                            <textarea name="shortgoals" rows="2" 
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"shortgoals"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>"><?= $id->shortgoals ?? '' ?></textarea>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">¿Cuáles son sus metas a largo plazo?</label>
                        <?php if ($is_hired) { ?>
                            <div class="<?= $read_class ?> whitespace-pre-wrap"><?= $id->longgoals ?? '' ?></div>
                        <?php } else { ?>
                            <textarea name="longgoals" rows="2" 
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"longgoals"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>"><?= $id->longgoals ?? '' ?></textarea>
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">¿Considera usted que es el candidato idóneo para este cargo? Indique sus razones</label>
                        <?php if ($is_hired) { ?>
                            <div class="<?= $read_class ?> whitespace-pre-wrap"><?= $id->reasons ?? '' ?></div>
                        <?php } else { ?>
                            <textarea name="reasons" rows="3" 
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"reasons"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                class="<?= $base_class ?>"><?= $id->reasons ?? '' ?></textarea>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">6. Información de Dotación (Tallas)</h2>
                
                <div class="grid md:grid-cols-3 gap-4">
                    
                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Talla Pantalón</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->talla_pantalon ?? 'N/A' ?></div>
                        <?php } else { ?>
                            <input type="text" name="talla_pantalon" 
                                value="<?= $id->talla_pantalon ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"talla_pantalon"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                placeholder="Ej: 32"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Talla Camisa</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->talla_camisa ?? 'N/A' ?></div>
                        <?php } else { ?>
                            <input type="text" name="talla_camisa" 
                                value="<?= $id->talla_camisa ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"talla_camisa"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                placeholder="Ej: M"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-600">Talla Zapatos</label>
                        <?php if ($is_hired) { ?>
                            <div class="truncate <?= $read_class ?>"><?= $id->talla_zapatos ?? 'N/A' ?></div>
                        <?php } else { ?>
                            <input type="text" name="talla_zapatos" 
                                value="<?= $id->talla_zapatos ?? '' ?>"
                                hx-post="?c=Recruitment&a=UpdateField"
                                hx-trigger="change delay:500ms"
                                hx-vals='{"id":<?= $id->id ?>,"field":"talla_zapatos"}'
                                hx-target="this"
                                hx-indicator="#loading"
                                placeholder="Ej: 40"
                                class="<?= $base_class ?>">
                        <?php } ?>
                    </div>

                </div>
            </section>

        </form>
    </div>

</body>

<script>

      const notyf = new Notyf({
        duration: 3000,
        position: {
          x: 'center',
          y: 'top',
        }
      });


      htmx.on("showMessage", (e) => {
        console.log(e);
          let data = JSON.parse(e.detail.value);
          let trigger = data.close;

          if (trigger) {
              let el = document.getElementById(trigger);
              if (el) el.click(); // solo hace click si el elemento existe
              let nt = document.getElementById('closeNestedModal');
              if (nt) nt.click(); // solo hace click si el elemento existe
          }

          notyf.success(data);
      });
</script>
