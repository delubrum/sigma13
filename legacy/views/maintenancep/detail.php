<div class="w-[98%] sm:w-[98%] h-[98vh] overflow-y-auto rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed" hx-boost="true">
    
    <div class="p-4">
        
        <div id='head' class="flex items-center justify-between mb-5 pb-3 border-b border-gray-200 gap-2"
            hx-get="?c=Maintenancep&a=Head&id=<?= $_REQUEST['id'] ?>"
            hx-trigger="load,refresh" hx-target="this">>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div id="info" class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=Maintenancep&a=Info&id=<?= $_REQUEST['id'] ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <div id="tab" class="p-4 bg-white rounded-lg shadow-md lg:col-span-3"
                hx-get="?c=Maintenancep&a=Tab&id=<?= $_REQUEST['id'] ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>
        </div>
    </div>
</div>