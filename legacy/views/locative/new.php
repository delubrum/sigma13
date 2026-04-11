<div class="w-[95%] max-h-[98vh] sm:w-[30%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">

  <!-- Close button -->
  <button
    id="closeNewModal"
    class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
    @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
    <i class="ri-close-line text-2xl"></i>
  </button>

  <!-- Title -->
  <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
    <i class="ri-tools-line text-2xl"></i>
    <span class="text-2xl font-semibold">New Machinery Service</span>
  </h1>

  <form
    id="newForm"
    enctype="multipart/form-data"
    class="space-y-5"
    hx-post="?c=Locative&a=Save"
    hx-swap="none"
    hx-indicator="#loading"
  >

    <!-- Hidden type -->
    <input type="hidden" name="kind" value="Locative">

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

    <!-- Machine -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">* Location</label>
      <select
        name="asset_id"
        required
        class="tomselect w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
      >
        <option value=""></option>
        <?php foreach ($this->model->list('id,hostname', 'assets', "and area = 'Locative' ORDER BY hostname ASC") as $r) { ?>
          <option value="0">Other</option>
          <option value="<?= $r->id ?>"><?= mb_convert_case($r->hostname, MB_CASE_TITLE, 'UTF-8') ?></option>
        <?php } ?>
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

<script>
  document.querySelectorAll('.tomselect').forEach(el => {
    new TomSelect(el);
  });
</script>