<div class="pt-4">
  <div class="w-full px-4">
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-2">

      <!-- Available -->
      <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-gray-200">
        <div>
          <div class="text-base sm:text-xl font-bold text-gray-900"><?= $a ?></div>
          <div class="text-sm text-gray-500">Available</div>
        </div>
        <div class="hidden sm:block text-black-500 bg-gray-100 rounded-full py-2 px-3 text-xl">
          <i class="ri-checkbox-circle-line"></i>
        </div>
      </div>

      <!-- Assigned -->
      <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-gray-200">
        <div>
          <div class="text-base sm:text-xl font-bold text-gray-900"><?= $b ?></div>
          <div class="text-sm text-gray-500">Assigned</div>
        </div>
        <div class="hidden sm:block text-black-500 bg-gray-100 rounded-full py-2 px-3 text-xl">
          <i class="ri-user-received-2-line"></i>
        </div>
      </div>

      <!-- Disposed -->
      <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-gray-200">
        <div>
          <div class="text-base sm:text-xl font-bold text-gray-900"><?= $c ?></div>
          <div class="text-sm text-gray-500">Disposed</div>
        </div>
        <div class="hidden sm:block text-black-500 bg-gray-100 rounded-full py-2 px-3 text-xl">
          <i class="ri-delete-bin-2-line"></i>
        </div>
      </div>

    </div>
  </div>
</div>
