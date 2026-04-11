<div class="w-[95%] sm:w-[25%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Reject Test <span></h1>

  <form autocomplete="off"
    id="formReject"
    hx-post="?c=Development&a=UpdateTask"
    hx-encoding="multipart/form-data"
    hx-indicator="#loading"
    class="space-y-6"
  >
    <input type="hidden" name="id" value="<?= $id->id ?>">

    <!-- Description -->
    <div>
      <label for="result" class="block text-sm font-medium text-gray-700 mb-1">Cause</label>
      <textarea 
        id="result"
        name="result"
        required
        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
        rows="3"
      ></textarea>
    </div>

      <!-- Images -->
    <div>
      <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Image (Not Required)</label>
      <input 
        id="file"
        type="file"
        name="file"
        accept="image/png"
        class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2 cursor-pointer focus:ring-2 focus:ring-black focus:outline-none"
      >
      <p class="text-xs text-gray-500 mt-1">Accepted formats: PNG</p>
    </div>

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