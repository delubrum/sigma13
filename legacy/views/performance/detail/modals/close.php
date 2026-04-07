<div class="w-[95%] sm:w-[25%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Close Plan <span></h1>

  <form autocomplete="off"
    id="formItem"
    hx-post="?c=Performance&a=PlanClose"
    hx-encoding="multipart/form-data"
    hx-indicator="#loading"
    class="space-y-6"
  >
    <input type="hidden" name="id" value="<?= $id->plan_id ?>">

    <div>
      <label class="block text-sm text-gray-600">* Status</label>
      <select required name="status" class="w-full p-2 border border-gray-300 rounded-md">
        <option value=""></option>
        <option value="completed">Completed</option>
        <option value="partial">Partial</option>
        <option value="incompleted">Incompleted</option>
      </select>
    </div>

    <div>
      <label class="block text-sm text-gray-600">Result</label>
      <textarea required name="result" class="w-full p-2 border border-gray-300 rounded-md"></textarea>
    </div>



    <div class="flex justify-end pt-4">
      <button type="submit"
              class="text-xl text-gray-900 font-semibold flex items-center gap-2 hover:text-gray-700 transition">
        <i class="ri-save-line"></i> Save
      </button>
    </div>
  </form>
</div>