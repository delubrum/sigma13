<div class="w-[98%] sm:w-[98%] h-[98vh] overflow-y-auto rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed"
     hx-boost="true">

    <!-- HEADER (SIN padding externo) -->
    <div id="head"
        class="flex items-center justify-between w-full p-4 border-b border-gray-200 bg-white shrink-0"
        hx-get="?c=Tickets&a=Head&id=<?= $_REQUEST['id'] ?>"
        hx-trigger="load,refresh"
        hx-target="this">
    </div>

    <!-- BODY -->
    <div class="p-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

            <div id="info"
                class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=Tickets&a=Info&id=<?= $_REQUEST['id'] ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <div id="tab"
                class="p-4 bg-white rounded-lg shadow-md lg:col-span-3"
                hx-get="?c=Tickets&a=Tab&id=<?= $_REQUEST['id'] ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

        </div>
    </div>
</div>
