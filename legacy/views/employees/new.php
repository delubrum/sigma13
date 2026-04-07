<div class="w-[95%] max-h-[98vh] sm:w-[50%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
  <button id="closeNewModal"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>

  <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
    <i class="ri-file-list-3-line text-2xl"></i>
    <span class="text-2xl font-semibold">New Employee</span>
  </h1>

  <form id="newForm"
    autocomplete="off"
    enctype="multipart/form-data"
    class="space-y-6"
    hx-post='?c=Employees&a=SaveEmployee'
    hx-swap="none"
    hx-indicator="#loading"
  >

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

      <div>
        <label class="block text-sm text-gray-600">*Type</label>
        <select required name="type" class="w-full p-2 border border-gray-300 rounded-md bg-white">
          <option value=""></option>
          <option value="Direct">Direct</option>
          <option value="Temporary">Temporary</option>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600">* CC</label>
        <input type="number" min="1" step="1" name="id" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>
      <div>
        <label class="block text-sm text-gray-600">* Name</label>
        <input type="text" name="name" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>
      <div>
          <label class="block text-sm text-gray-600">* Job Position</label>
          <select required name="profile" class="tomselect w-full p-2 border border-gray-300 rounded-md">
              <option value=""></option>
              <?php foreach ($this->model->list('id,name,division_id', 'job_profiles', 'ORDER BY name ASC') as $r) { ?>
                  <option value="<?= $r->id ?>"><?= $r->name ?> || <?= $this->model->get('name', 'hr_db', "and id = $r->division_id")->name ?></option>
              <?php } ?>
          </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600">* City</label>
        <select required name="city" class="w-full p-2 border border-gray-300 rounded-md">
          <option value=""></option>
          <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'city'") as $r) { ?>
            <option value="<?= $r->name ?>"><?= $r->name ?></option>
          <?php } ?>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600">* Talla Pantalón</label>
        <input name="talla_pantalon" type="text" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-sm text-gray-600">* Talla Camisa</label>
        <input name="talla_camisa" type="text" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-sm text-gray-600">* Talla Zapatos</label>
        <input name="talla_zapatos" type="text" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>
    </div>
    <div class="flex justify-end pt-4">
      <button type="submit"
              class="text-xl text-gray-900 font-semibold flex items-center gap-2 hover:text-gray-700 transition">
        <i class="ri-save-line"></i> Save
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
    create: false
  });
});
</script>