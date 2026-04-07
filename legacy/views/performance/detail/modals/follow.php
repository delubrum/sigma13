<div class="w-[95%] sm:w-[95%] bg-white p-4 rounded-lg shadow-lg relative z-50">

    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <h1 class="text-black-700 mb-6">
        <i class="ri-add-line text-2xl"></i>
        <span class="text-2xl font-semibold">Follow Plan</span>
    </h1>

        <?php if (is_null($this->model->get('closed_at', 'test_plans', "and id = $id->plan_id")->closed_at)) {  ?>

    <form
        id="followForm"
        hx-post="?c=Performance&a=FollowSave"
        hx-on::after-request="htmx.trigger('refresh')"
        hx-indicator="#loading"
        class="space-y-4 mb-8 border-b pb-4" 
    >


        <input type="hidden" name="plan_id" value="<?= $id->plan_id ?>">

        <div class="space-y-1">
            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                <div id="bar" class="bg-green-500 h-full transition-all duration-300" style="width: <?= $id->progress ?? 0 ?>%;"></div>
            </div>
        </div>


        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0"> 
            
            <div class="w-full sm:w-1/6">
                <label class="text-xs font-medium text-gray-700 block">Progress (%)</label>
                <div class="flex items-center space-x-2">
                    <input 
                        required
                        type="number" 
                        name="progress"
                        min="<?= $id->progress ?? 0 ?>" max="100"
                        class="w-full px-2 py-2 border border-gray-300 rounded-md text-xs"
                        value = "<?= $id->progress ?? 0 ?>"
                        oninput="document.getElementById('bar').style.width = this.value + '%'"
                    >
                </div>
            </div>

            <div class="w-full sm:w-3/6">
                <label class="text-xs font-medium text-gray-700 block">Notes</label>
                <input type="text" name="notes"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    placeholder="Write a note..."
                >
            </div>

            <div class="w-full sm:w-2/6">
                <label class="text-xs font-medium text-gray-700 block">Next follow date</label>
                <input 
                    required
                    type="date" 
                    name="follow"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
            </div>

            <button type="submit"
                class="w-full sm:w-auto px-4 py-2 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-gray-800 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50 **mt-4 sm:mt-6**"
            >
                <i class="ri-send-plane-line"></i> <span>Save</span>
            </button>


        </div>
        </form>

                <?php }  ?>

    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-4">Follow-up History</h2>
    <table id="follow-results"
        class="w-full border-collapse rounded-md overflow-hidden border border-gray-200 text-xs"
        hx-get="?c=Performance&a=GetFollow&id=<?= $id->plan_id ?>"
        hx-trigger="load, refresh"
        hx-target="this"
    >
        <!-- HTMX reemplaza TODO el contenido -->
    </table>
</div>