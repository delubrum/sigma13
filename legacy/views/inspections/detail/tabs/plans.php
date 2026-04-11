<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-file-search-line text-xl"></i>
        <span>Plans</span>
    </h2>

    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
        <input type="search" id="searchEvent" 
            class="w-full sm:w-2/3 px-3 py-2 border border-gray-300 rounded-md text-xs shadow-inner focus:outline-none focus:ring-2 focus:ring-gray-500"
            placeholder="Search..."
            name="search"
            hx-post="?c=Infraimprovement&a=GetEvents&kind=plan&id=<?= $id->id ?>"
            hx-trigger="input changed delay:500ms, keyup[key=='Enter'], load"
            hx-target="#search-results"
            hx-indicator="#searching">        
        
        <button 
            class="w-full sm:w-auto px-3 py-1.5 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-black text-white hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50"
            hx-get="?c=Infraimprovement&a=DetailModal&modal=add-plan&id=<?= $id->id ?>"
            hx-indicator="#loading"
            hx-target="#nestedModal"
            @click='nestedModal = true' 
            >
            <i class="ri-add-line text-xs"></i>
            <span>Add Plan</span>
        </button>
    </div>

    <div id="searching" class="htmx-indicator flex items-center gap-2 text-sm text-gray-500 transition-opacity duration-300 opacity-0">
        <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Searching...</span>
    </div>

    <div class="overflow-auto max-h-[40vh]">
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Description</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Date</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">User</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Start</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">End</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Rating</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Actions</th>
                </tr>
            </thead>
            <tbody id="search-results" 
                class="overflow-auto max-h-[65vh]p-2"
                hx-get="?c=Infraimprovement&a=GetEvents&kind=plan&id=<?= $id->id ?>"
                hx-trigger="load, refresh"
                hx-target="this">
            </body>
        </table>
    </div>
</div>
