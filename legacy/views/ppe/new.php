<div class="w-[95%] max-h-[98vh] sm:w-[40%] bg-white p-4 rounded-lg shadow-lg relative z-50 overflow-y-auto">

  <!-- Botón de cierre -->
  <button id="closeNewModal"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
    <i class="ri-close-line text-2xl"></i>
  </button>

  <!-- Título -->
  <h1 class="text-black-700 mb-4 flex items-center gap-2">
    <i class="ri-shield-user-line text-2xl"></i>
    <span class="text-2xl font-semibold">
      <?php echo isset($id) ? 'Edit PPE' : 'New PPE'; ?>
    </span>
  </h1>

  <!-- Formulario -->
  <form id="newForm"
    enctype="multipart/form-data"
    class="p-4 space-y-6"
    hx-post='?c=PPEDB&a=Save'
    hx-swap="none"
    hx-indicator="#loading"
  >
    <!-- hidden id -->
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id->id : '' ?>">

    <!-- Sección: Datos del producto -->
    <div>
      <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">

        <!-- Name -->
        <div>
          <label class="block text-sm text-gray-600">* Name</label>
          <input required type="text" name="name"
            value="<?php echo isset($id) ? htmlspecialchars($id->name) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <!-- Code -->
        <div>
          <label class="block text-sm text-gray-600">* Code</label>
          <input name="code"
            value="<?php echo isset($id) ? htmlspecialchars($id->code ?? '') : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <!-- Price -->
        <div>
          <label class="block text-sm text-gray-600">* Price</label>
          <input name="price"
            value="<?php echo isset($id) ? htmlspecialchars($id->price ?? '') : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <!-- Min Stock -->
        <div>
          <label class="block text-sm text-gray-600">* Minimum Stock</label>
          <input required type="number" name="min"
            value="<?php echo isset($id) ? htmlspecialchars($id->min) : '' ?>"
            class="w-full p-2 border border-gray-300 rounded-md">
        </div>

      </div>
    </div>

    <!-- Botón -->
    <div class="mt-6 flex justify-end">
      <button type="submit" 
        class="text-xl text-gray-900 font-bold hover:text-gray-700 flex items-center gap-1">
        <i class="ri-save-line"></i>
        <?php echo isset($id) ? 'Update' : 'Save'; ?>
      </button>
    </div>
  </form>
</div>
