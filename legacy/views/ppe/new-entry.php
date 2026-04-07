<div class="w-[95%] max-h-[98vh] sm:w-[25%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
  <button id="closeNewModal"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>

  <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
    <i class="ri-add-box-line text-2xl"></i>
    <span class="text-2xl font-semibold">New Entry</span>
  </h1>

  <form id="new_form"
    autocomplete="off"
    enctype="multipart/form-data"
    class="space-y-6"
    hx-post="?c=PPEEntries&a=Save"
    hx-swap="none"
    hx-indicator="#loading"
    onsubmit="setTimeout(() => { location.reload(); }, 1000);"
  >

    <div class="flex flex-col space-y-4">
      
      <div>
        <label class="block text-sm text-gray-600 font-medium mb-1">* Item:</label>
        <select name="item_id" required class="tomselect w-full p-2 border border-gray-300 rounded-md">
            <option value=""></option>
            <?php foreach ($this->model->list('*', 'epp_db', ' ORDER BY name') as $r) { ?>
                <option value="<?= $r->id ?>"><?= $r->name ?></option>
            <?php } ?>
        </select>
      </div>

      <div>
        <label class="block text-sm text-gray-600 font-medium mb-1">* Cantidad:</label>
        <input type="number" name="qty" required class="w-full p-2 border border-gray-300 rounded-md">
      </div>

    </div>

    <div class="flex justify-end pt-4 border-t">
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
    create: false,
    placeholder: "Seleccione un item..."
  });
});
</script>