<div class="w-[95%] sm:w-[25%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Add Plan <span></h1>

  <form autocomplete="off"
    id="formItem"
    hx-post="?c=Performance&a=PlanSave"
    hx-encoding="multipart/form-data"
    hx-indicator="#loading"
    class="space-y-6"
  >
    <input type="hidden" name="user_id" value="<?= $id->id ?>">

    <div>
      <label class="block text-sm text-gray-600">* Competency</label>
      <select required name="competency" class="tomselect w-full p-2 border border-gray-300 rounded-md">
        <option value=""></option>
        <option value="Desempeño General">Desempeño General</option>
        <option value="Atención al Detalle">Atención al Detalle</option>
        <option value="Capacidad de planificación y organización">Capacidad de planificación y organización</option>
        <option value="Compromiso">Compromiso</option>
        <option value="Comunicación Efectiva">Comunicación Efectiva</option>
        <option value="Conocimiento Técnico">Conocimiento Técnico</option>
        <option value="Conocimientos técnicos">Conocimientos técnicos</option>
        <option value="Competencias Técnicas">Competencias Técnicas</option>
        <option value="Dirección de equipos de trabajo">Dirección de equipos de trabajo</option>
        <option value="Ética y Responsabilidad">Ética y Responsabilidad</option>
        <option value="Gestión y logro de objetivos">Gestión y logro de objetivos</option>
        <option value="Liderazgo">Liderazgo</option>
        <option value="Orientación a la Calidad">Orientación a la Calidad</option>
        <option value="Orientación a los resultados con calidad">Orientación a los resultados con calidad</option>
        <option value="Orientación a Resultados">Orientación a Resultados</option>
        <option value="Pensamiento Analítico">Pensamiento Analítico</option>
        <option value="Resolución de Problemas">Resolución de Problemas</option>
        <option value="Responsabilidad">Responsabilidad</option>
        <option value="Tolerancia a la Presión">Tolerancia a la Presión</option>
        <option value="Trabajo Colaborativo">Trabajo Colaborativo</option>
        <option value="Autonomía">Autonomía</option>
        <option value="Visión Estratégica">Visión Estratégica</option>
      </select>
    </div>

    <div>
      <label class="block text-sm text-gray-600">Plan</label>
      <textarea required name="plan" class="w-full p-2 border border-gray-300 rounded-md"></textarea>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Start Date:</label>
      <input
        type="date"
        name="started_at"
        max='2026-04-01'
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* End Date: <span class="text-red-500">(1 April MAX)</span></label>
      <input
        type="date"
        name="ended_at"
        max='2026-04-01'
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Next Follow Date:</label>
      <input
        type="date"
        name="follow"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div class="flex justify-end pt-4">
      <button type="submit"
              class="text-xl text-gray-900 font-semibold flex items-center gap-2 hover:text-gray-700 transition">
        <i class="ri-save-line"></i> Save
      </button>
    </div>
  </form>
</div>