<div class="w-[95%] sm:w-[50%] bg-white p-4 rounded-lg shadow-lg relative z-50">
  <!-- Close Button (X) in Top-Right Corner -->
  <button id="closeNestedModal"
    class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
    @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
  >
      <i class="ri-close-line text-2xl"></i>
  </button>
  <h1 class="text-black-700"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Close Plan <span></h1>
  <form  id="newForm" 
    enctype="multipart/form-data"
    class="overflow-y-auto max-h-[600px] p-4"
    hx-post='?c=Infraimprovement&a=UpdateEvent' 
    hx-swap="none"
    hx-indicator="#loading">
  
    <input type='hidden' name='id' value="<?= $id->id ?>">

    <div class="grid grid-cols-1">

          <div>
        <label for="end" class="block text-gray-600 text-sm mt-4">End</label>
        <input onclick="this.showPicker()" type="date" required id="end" name="end" class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none"></input>
      </div>

    <div>
        <label for="rating" class="block text-sm mt-4">Rating</label>
        <select required id="rating" name="rating" class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none">
          <option value="" disabled selected></option>
          <option value="1">1 - Very Poor</option>
          <option value="2">2 - Poor</option>
          <option value="3">3 - Fair</option>
          <option value="4">4 - Good</option>
          <option value="5">5 - Excellent</option>
        </select>
      </div>

      <div>
          <label for="description" class="block text-gray-600 text-sm mt-4">Notes</label>
          <textarea id="description" name="description" class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black focus:outline-none"></textarea>
      </div>

      <div>
          <label for="evidence" class="block text-gray-600 text-sm mt-4">Evidence</label>
          <input id="evidence" name="files[]" type="file" >
      </div>

    </div>

    <div class="mt-6 flex justify-end">
      <button type="submit" class="text-xl text-gray-900 font-bold hover:text-gray-700">
        <i class="ri-save-line"></i> Save
      </button>
    </div>
  </form>
</div>
