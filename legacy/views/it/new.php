<div class="w-[95%] max-h-[98vh] sm:w-[30%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
  <button id="closeNewModal"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>

  <!-- Title -->
  <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
    <i class="ri-tools-line text-2xl"></i>
    <span class="text-2xl font-semibold">New Service Desk</span>
  </h1>

  <form
    id="newForm"
    enctype="multipart/form-data"
    class="space-y-5"
    hx-post="?c=IT&a=Save"
    hx-swap="none"
    hx-indicator="#loading"
  >

    <!-- Facility -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">* Facility</label>
      <select
        name="facility"
        required
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      >
        <option value=""></option>
        <option value="ESM1">ESM1</option>
        <option value="ESM2">ESM2</option>
        <option value="ESM3">ESM3</option>
        <option value="Medellín">Medellín</option>
      </select>
    </div>

    <!-- Type -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">* Type</label>
      <select
        name="kind"
        id="type"
        required
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      >
        <option value=""></option>
        <option value="Equipment/Accessories">Equipment / Accessories</option>
        <option value="Licenses">Licenses</option>
        <option value="Permissions">Permissions</option>
      </select>
    </div>

    <!-- Level (hidden by default) -->
    <div id="levelContainer" class="hidden">
      <label class="block text-sm font-medium text-gray-700 mb-1">* Level</label>
      <select
        name="urgency_level"
        id="level"
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      >
        <option value=""></option>
        <option value="1">Nivel 1: Diseñadores, Ing. Estructurales, Programadores</option>
        <option value="2">Nivel 2: Costeadores, Administradores del Proceso</option>
        <option value="3">Nivel 3: Gerencias, Direcciones, Administrativo</option>
      </select>
    </div>

    <!-- Priority -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">* Priority</label>
      <select
        name="priority"
        required
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      >
        <option value=""></option>
        <option value="High">Right Now. Locked</option>
        <option value="Medium">Today. Need Attention</option>
        <option value="Low">Tomorrow. I Can Wait</option>
      </select>
    </div>

    <!-- Description -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">* Description</label>
      <textarea
        name="description"
        required
        rows="3"
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      ></textarea>
    </div>

    <!-- Picture -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Picture</label>
      <input
        type="file"
        name="files[]"
        accept="image/*"
        capture
        class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2 cursor-pointer focus:ring-2 focus:ring-black focus:outline-none"
      >
    </div>

    <!-- Submit -->
    <div class="flex justify-end">
      <button
        type="submit"
        class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
      >
        <i class="ri-save-line"></i> Save
      </button>
    </div>

  </form>
</div>
