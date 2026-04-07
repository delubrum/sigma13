<div class="w-[95%] sm:w-[25%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Add Candidate <span></h1>

  <form autocomplete="off"
    id="formItem"
    hx-post="?c=Recruitment&a=SaveCandidate"
    hx-encoding="multipart/form-data"
    hx-indicator="#loading"
    hx-on="htmx:afterRequest: document.getElementById('nestedModal').innerHTML = ''; if (typeof table !== 'undefined') table.ajax.reload(null, false);"
    class="space-y-6"
  >
    <input type="hidden" name="recruitment_id" value="<?= $id->id ?>">

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Name:</label>
      <input
        type="text"
        name="name"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* CC:</label>
      <input
        type="number"
        name="cc"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Email:</label>
      <input
        type="email"
        name="email"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Phone:</label>
      <input
        type="text"
        name="phone"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">* Appointment:</label>
      <input
        id="datetime"
        type="text"
        name="appointment"
        placeholder="Select date and time"
        required
        class="w-full p-2 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
      >
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
  // Activar Flatpickr en el input
  flatpickr("#datetime", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minuteIncrement: 5,
    minDate: "today",
    allowInput: true 
  });
</script>