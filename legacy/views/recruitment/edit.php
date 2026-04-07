<div class="w-[95%] max-h-[98vh] sm:w-[80%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
  <button id="closeNewModal"
      class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
      @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>

  <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
    <i class="ri-edit-line text-2xl"></i>
    <span class="text-2xl font-semibold">Edit Recruitment (ID: <?= $recruitment->id ?>)</span>
  </h1>

  <form id="editForm"
    autocomplete="off"
    enctype="multipart/form-data"
    class="space-y-6"
    hx-post='?c=Recruitment&a=Update&id=<?= $recruitment->id ?>'
    hx-swap="none"
    hx-indicator="#loading"
  >

  <?php if (! empty($recruitment->rejection)) { ?>
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6 flex items-start space-x-3" role="alert">
      <i class="ri-alert-line text-2xl mt-0.5"></i>
      <div>
          <strong class="font-bold">Motivo del Rechazo:</strong>
          <p class="text-sm mt-1"><?= htmlspecialchars($recruitment->rejection) ?></p>
      </div>
  </div>
  <?php } ?>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="sm:col-span-3">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600">* Job Position</label>
                <select required name="profile_id"
                        class="tomselect w-full p-2 border border-gray-300 rounded-md"
                        hx-get="?c=Recruitment&a=getJobDetails&selected_id=<?= $recruitment->profile_id ?>"
                        hx-trigger="change"
                        hx-target="#job-details"
                        hx-swap="innerHTML"
                >
                    <option value=""></option>
                    <?php foreach ($this->model->list('id,name,division_id', 'job_profiles', 'ORDER BY name ASC') as $r) { ?>
                        <option value="<?= $r->id ?>" <?= $r->id == $recruitment->profile_id ? 'selected' : '' ?>>
                            <?= $r->name ?> || <?= $this->model->get('name', 'hr_db', "and id = $r->division_id")->name ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
              <label class="block text-sm text-gray-600">* City</label>
              <select required name="city" class="w-full p-2 border border-gray-300 rounded-md">
                <option value=""></option>
                <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'city'") as $r) { ?>
                    <option value="<?= trim($r->name) ?>" <?= (trim($r->name) == trim($recruitment->city)) ? 'selected' : '' ?>>
                    <?= $r->name ?>
                  </option>
                <?php } ?>
              </select>
            </div>
        </div>

        <div id="job-details" class="p-4">
            <p class="text-sm text-gray-500">Select a position to view the experience and education requirements.</p>
        </div>
      </div>
      <div>
        <label class="block text-sm text-gray-600">* Quantity</label>
        <input type="number" min="1" step="1" name="qty" required class="w-full p-2 border border-gray-300 rounded-md" value="<?= $recruitment->qty ?>">
      </div>

      <div>
        <label class="block text-sm text-gray-600">* Contract</label>
        <select required name="contract" class="w-full p-2 border border-gray-300 rounded-md">
          <option value=""></option>
          <?php foreach ($this->model->list('*', 'hr_db', " and kind = 'contract'") as $r) { ?>
              <option value="<?= trim($r->name) ?>" <?= (trim($r->name) == trim($recruitment->contract)) ? 'selected' : '' ?>>
              <?= $r->name ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <div>
        <label class="block text-sm text-gray-600">* Salary</label>
        <input required name="srange" class="w-full p-2 border border-gray-300 rounded-md" value="<?= htmlspecialchars($recruitment->srange ?? '') ?>">
      </div>

      <div>
        <label class="block text-sm text-gray-600">* Start Date</label>
        <input type="date" onfocus="this.showPicker()" name="start_date" class="w-full p-2 border border-gray-300 rounded-md" value="<?= $recruitment->start_date ?>">
      </div>

      <div x-data="{ cause: '<?= addslashes($recruitment->cause ?? '') ?>' }">
        <div>
          <label class="block text-sm text-gray-600">* Cause</label>
          <select x-model="cause" required name="cause"
                  class="w-full p-2 border border-gray-300 rounded-md">
            <option value=""></option>
            <?php foreach ($this->model->list('*', 'hr_db', " AND kind = 'cause' ORDER BY name ASC") as $r) { ?>
                <option value="<?= trim($r->name) ?>" <?= (trim($r->name) == trim($recruitment->cause)) ? 'selected' : '' ?>>
                <?= $r->name ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div x-show="cause == 'Remplazo' || cause == 'Remplazo por Maternidad / Incapacidad'" x-transition>
          <label class="block text-sm text-gray-600">* Replaces</label>
          <input name="replaces"
                 x-bind:required="cause == 'Remplazo' || cause == 'Remplazo por Maternidad / Incapacidad'"
                 class="w-full p-2 border border-gray-300 rounded-md"
                 value="<?= htmlspecialchars($recruitment->replaces ?? '') ?>">
        </div>
      </div>

    </div>

    <div>
      <label class="block text-sm text-gray-600">Others</label>
      <textarea required name="others" rows="3" class="w-full p-2 border border-gray-300 rounded-md"><?= htmlspecialchars($recruitment->others ?? '') ?></textarea>
    </div>

    <div>
      <label class="block text-sm text-gray-600">Candidates (.zip)</label>
      <input type="file" name="file" accept=".zip" class="w-full p-2 border border-gray-300 rounded-md">
      <?php if (! empty($recruitment->file_path)) { ?>
          <p class="text-xs text-gray-500 mt-1">Current file: <a href="<?= $recruitment->file_path ?>" target="_blank" class="text-blue-500 hover:underline">Download</a> (Upload new file to replace)</p>
      <?php } ?>
    </div>

    <div class="flex justify-end pt-4">
      <button type="submit"
              class="text-xl text-gray-900 font-semibold flex items-center gap-2 hover:text-gray-700 transition">
        <i class="ri-edit-line"></i> Update
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


document.addEventListener('DOMContentLoaded', function() {
    const jobSelect = document.querySelector('select[name="profile_id"]');
    if (jobSelect && jobSelect.value) {
        htmx.trigger(jobSelect, 'change');
    }
});
</script>