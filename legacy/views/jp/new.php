<div class="w-[95%] max-h-[98vh] sm:w-[80%] bg-white p-4 rounded-lg shadow-lg relative z-50 overflow-y-auto">
  <!-- Botón de cierre -->
  <button id="<?= $buttonId ?>"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="<?= $modalVar ?> = !<?= $modalVar ?>; document.getElementById('<?= $modalDiv ?>').innerHTML = '';"
  >
    <i class="ri-close-line text-2xl"></i>
  </button>

  <!-- Título dinámico -->
  <h1 class="text-black-700">
    <i class="ri-briefcase-line text-2xl"></i>
    <span class="text-2xl font-semibold">
      <?php echo isset($id) ? 'Editar Perfil' : 'Nuevo Perfil'; ?>
    </span>
  </h1>

  <!-- Formulario -->
  <form id="newForm"
    enctype="multipart/form-data"
    class="p-4 space-y-4"
    hx-post='?c=JP&a=Save'
    hx-swap="none"
    hx-indicator="#loading"
  >
    <!-- hidden id -->
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id->id : '' ?>">

    <!-- Identificación del cargo -->
    <div>
      <h2 class="text-lg font-semibold text-gray-700 mb-4">1. Identificación del cargo</h2>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div>
          <label class="block text-sm text-gray-600">Código</label>
          <input required type="text" name="code"
            value="<?php echo isset($id) ? htmlspecialchars($id->code) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>
        
        <div>
          <label class="block text-sm text-gray-600">Nombre del cargo</label>
          <input required type="text" name="name"
            value="<?php echo isset($id) ? htmlspecialchars($id->name) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div>
          <label class="block text-sm text-gray-600">División</label>
          <select required name="division_id" class="tomselect w-full p-2 border border-gray-300 rounded-md">
            <option value=''></option>
            <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'division' ORDER BY name ASC") as $r) { ?>     
              <option value='<?php echo $r->id?>' <?php echo (isset($id) && $id->division_id == $r->id) ? 'selected' : '' ?>><?php echo $r->name?></option>
            <?php } ?>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Cargo a quien reporta</label>
          <select required name="reports_to" class="tomselect w-full p-2 border border-gray-300 rounded-md">
            <option value=''></option>
            <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'position' ORDER BY name ASC") as $r) { ?>     
              <option 
                value='<?php echo htmlspecialchars($r->id, ENT_QUOTES, 'UTF-8'); ?>' 
                <?php echo (isset($id) && $id->reports_to == $r->id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($r->name, ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php } ?>
          </select>
        </div>

<div>
  <label class="block text-sm text-gray-600">Cargos que le reportan</label>
  <select multiple required name="reports[]" 
    class="tomselect w-full p-2 border border-gray-300 rounded-md">
    <?php
      // Opciones: lista de cargos posibles
      foreach ($this->model->list('*', 'hr_db', " and kind = 'position' ORDER BY name ASC") as $r) {
          // Si hay valores seleccionados (por ejemplo, guardados como JSON o CSV)
          $selectedReports = isset($id) && ! empty($id->reports) ? explode(',', $id->reports) : [];
          $selected = in_array($r->id, $selectedReports) ? 'selected' : '';
          ?>
      <option value="<?php echo htmlspecialchars($r->id, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>>
        <?php echo htmlspecialchars($r->name, ENT_QUOTES, 'UTF-8'); ?>
      </option>
    <?php } ?>
  </select>
</div>


        <div>
          <label class="block text-sm text-gray-600">Modalidad de trabajo</label>
          <select required name="work_mode"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value='' disabled <?php echo ! isset($id) ? 'selected' : '' ?>></option>
            <option <?php echo (isset($id) && $id->work_mode == 'Presencial') ? 'selected' : '' ?>>Presencial</option>
            <option <?php echo (isset($id) && $id->work_mode == 'Teletrabajo') ? 'selected' : '' ?>>Teletrabajo</option>
            <option <?php echo (isset($id) && $id->work_mode == 'Remoto') ? 'selected' : '' ?>>Remoto</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Nivel Jerárquico</label>
          <select required name="rank"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value='' disabled <?php echo ! isset($id) ? 'selected' : '' ?>></option>
            <option value="Junta Directiva" <?php echo (isset($id) && $id->rank == 'Junta Directiva') ? 'selected' : '' ?>>Junta Directiva</option>
            <option value="Alta Dirección" <?php echo (isset($id) && $id->rank == 'Alta Dirección') ? 'selected' : '' ?>>Alta Dirección</option>
            <option value="Gerencias" <?php echo (isset($id) && $id->rank == 'Gerencias') ? 'selected' : '' ?>>Gerencias</option>
            <option value="Directores" <?php echo (isset($id) && $id->rank == 'Directores') ? 'selected' : '' ?>>Directores</option>
            <option value="Jefes de Área" <?php echo (isset($id) && $id->rank == 'Jefes de Área') ? 'selected' : '' ?>>Jefes de Área</option>
            <option value="Personal Administrativo" <?php echo (isset($id) && $id->rank == 'Personal Administrativo') ? 'selected' : '' ?>>Personal Administrativo</option>
            <option value="Aprendices" <?php echo (isset($id) && $id->rank == 'Aprendices') ? 'selected' : '' ?>>Aprendices</option>
            <option value="Personal Operativo" <?php echo (isset($id) && $id->rank == 'Personal Operativo') ? 'selected' : '' ?>>Personal Operativo</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Horario de trabajo</label>
          <input required type="text" name="schedule"
            value="<?php echo isset($id) ? htmlspecialchars($id->schedule) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div>
          <label class="block text-sm text-gray-600">Disponibilidad para viajar</label>
          <select required name="travel"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value='' disabled <?php echo ! isset($id) ? 'selected' : '' ?>></option>
            <option value="SI" <?php echo (isset($id) && $id->travel == 'SI') ? 'selected' : '' ?>>SI</option>
            <option value="NO" <?php echo (isset($id) && $id->travel == 'NO') ? 'selected' : '' ?>>NO</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Disponibilidad cambio residencia</label>
          <select required name="relocation"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value='' disabled <?php echo ! isset($id) ? 'selected' : '' ?>></option>
            <option value="SI" <?php echo (isset($id) && $id->relocation == 'SI') ? 'selected' : '' ?>>SI</option>
            <option value="NO" <?php echo (isset($id) && $id->relocation == 'NO') ? 'selected' : '' ?>>NO</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600">Experiencia</label>
          <input required type="text" name="experience"
            value="<?php echo isset($id) ? htmlspecialchars($id->experience) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div>
          <label class="block text-sm text-gray-600">Convalidaciones u Observaciones</label>
          <input required type="text" name="obs"
            value="<?php echo isset($id) ? htmlspecialchars($id->obs) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div>
          <label class="block text-sm text-gray-600">Idiomas</label>
          <select required name="lang"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value='' disabled <?php echo ! isset($id) ? 'selected' : '' ?>></option>
            <option value="N/A" <?php echo (isset($id) && $id->lang == 'N/A') ? 'selected' : '' ?>>N/A</option>
            <option value="Inglés Básico" <?php echo (isset($id) && $id->lang == 'Inglés Básico') ? 'selected' : '' ?>>Inglés Básico</option>
            <option value="Inglés Medio" <?php echo (isset($id) && $id->lang == 'Inglés Medio') ? 'selected' : '' ?>>Inglés Medio</option>
            <option value="Inglés Avanzado" <?php echo (isset($id) && $id->lang == 'Inglés Avanzado') ? 'selected' : '' ?>>Inglés Avanzado</option>
          </select>
        </div>

      </div>
    </div>

    <!-- Misión -->
    <div>
      <h2 class="text-lg font-semibold text-gray-700 mb-4">2. Misión del cargo</h2>
      <textarea required name="mission" rows="4"
        class="w-full p-2 border border-gray-300 rounded-md"><?php echo isset($id) ? htmlspecialchars($id->mission) : '' ?></textarea>
    </div>

    <!-- Botón -->
    <div class="mt-6 flex justify-end">
      <button type="submit" class="text-xl text-gray-900 font-bold hover:text-gray-700">
        <i class="ri-save-line"></i> <?php echo isset($id) ? 'Actualizar' : 'Guardar'; ?>
      </button>
    </div>
  </form>
</div>

<script>
document.querySelectorAll('.tomselect').forEach(el => {
  new TomSelect(el, {
    openOnFocus: true,
    maxOptions: null,
    diacritics: true,
    highlight: true,
    create: false,
    plugins: ['remove_button'], // activa el botón de eliminar para selects múltiples
  });
});
</script>