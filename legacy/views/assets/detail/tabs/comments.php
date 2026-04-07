<div>
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-chat-3-line text-xl"></i>
        <span>Comments</span>
    </h2>

    <form 
    hx-post="?c=Assets&a=SaveEvent"
    hx-on::after-request="htmx.trigger('#search-results', 'refresh')"
    hx-indicator="#loading"
    class="flex items-center space-x-2 mb-8"
    >
        <input type='hidden' name='asset_id' value="<?= $id->id ?>">
        <input type='hidden' name='kind' value="comment">

        <input type="text" name="description"
        class="flex-grow px-3 py-2 border border-gray-300 rounded-md text-xs text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500" 
        placeholder="Write a comment..."
        >
        <button type="submit" class="px-3 py-1.5 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-gray-800 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50">
            <i class="ri-send-plane-line"></i> <span>Save</span>
        </button>
    </form>

    <input type="search" id="searchEvent" class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs mb-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-gray-500" 
        placeholder="Search..."
        name="search"
        hx-post="?c=Assets&a=GetEvents&kind=comment&id=<?= $id->id ?>"
        hx-trigger="input changed delay:500ms, keyup[key=='Enter'], load"
        hx-target="#search-results"
        hx-indicator="#searching">

    <div id="searching" class="htmx-indicator flex items-center gap-2 text-sm text-gray-500 transition-opacity duration-300 opacity-0">
        <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>Searching...</span>
    </div>

    <div id="search-results"
        class="overflow-auto max-h-[50vh] space-y-4 p-2"
        hx-get="?c=Assets&a=GetEvents&kind=comment&id=<?= $id->id ?>"
        hx-trigger="load, refresh"
        hx-target="this"
        >
    </div>
</div>
