<div class="w-[95%] sm:w-[50%] bg-white p-4 rounded-lg shadow-lg relative z-50">
  <!-- Close Button (X) in Top-Right Corner -->
  <button id="closeNestedModal"
    class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
    @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>
  <h1 class="text-black-700"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> New Plan <span></h1>
  <form  id="newForm" 
    enctype="multipart/form-data"
    class="overflow-y-auto max-h-[600px] p-4"
    hx-post='?c=Infraimprovement&a=SaveEvent' 
    hx-swap="none"
    hx-indicator="#loading">
  
    <input type='hidden' name='infraimprovement_id' value="<?= $id->id ?>">
    <input type='hidden' name='type' value="plan">

    <div class="grid grid-cols-1">

      <div>
        <label for="description" class="block text-gray-600 text-sm mt-4">Description</label>
        <textarea required id="description" name="description" class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none"></textarea>
      </div>

      <div>
        <label for="responsible_id" class="block text-gray-600 text-sm mt-4">Responsible</label>
        <select id="responsible_id" name="responsible_id" class="tomselect w-full bg-white p-[9px] w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none" required>
          <option value='' disabled selected></option>
          <?php foreach ($this->model->list('*', 'employees', ' ORDER BY name ASC') as $r) { ?>     
            <option value='<?php echo $r->id?>'><?php echo $r->name?></option>
          <?php } ?>
        </select>
      </div>

      <div>
        <label for="start" class="block text-gray-600 text-sm mt-4">Start</label>
        <input onclick="this.showPicker()" type="date" required id="start" name="start" class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none"></input>
      </div>


    </div>

    <div class="mt-6 flex justify-end">
      <button type="submit" class="text-xl text-gray-900 font-bold hover:text-gray-700">
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
