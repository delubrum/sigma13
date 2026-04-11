<div x-data="{ software: @js($data['software'] ?? []), newSw: '' }" class="w-full">
    <div class="grid grid-cols-2 gap-2 mb-3">
        @php $baseSw = ['Office 365', 'Autodesk', 'Adobe', 'Bluebeam', 'Smard2d', 'Sap Erp', 'Hilti', 'Idea Statica', 'Sap 2000', 'Rfam', '3Dexperience']; @endphp
        @foreach($baseSw as $sw)
            <label class="flex items-center gap-2 p-2 rounded-lg border border-sigma-b/50 bg-sigma-bg2/30 hover:bg-sigma-bg2 cursor-pointer transition-all group">
                <input type="checkbox" name="software[]" value="{{ $sw }}" 
                       {{ in_array($sw, $data['software'] ?? []) ? 'checked' : '' }}
                       class="rounded-sm border-sigma-b text-sigma-ac focus:ring-sigma-ac/20 w-3 h-3 bg-sigma-bg">
                <span class="text-[9px] font-bold text-sigma-tx2 group-hover:text-sigma-tx uppercase truncate">{{ $sw }}</span>
            </label>
        @endforeach
        <template x-for="(item, index) in software.filter(i => !@js($baseSw).includes(i))" :key="index">
            <label class="flex items-center gap-2 p-2 rounded-lg border border-sigma-ac/30 bg-sigma-bg2 hover:bg-sigma-bg2 cursor-pointer transition-all group animate-core">
                <input type="checkbox" name="software[]" :value="item" checked
                       class="rounded-sm border-sigma-b text-sigma-ac focus:ring-sigma-ac/20 w-3 h-3 bg-sigma-bg">
                <span class="text-[9px] font-bold text-sigma-tx uppercase truncate" x-text="item"></span>
                <button type="button" @click="software = software.filter(i => i !== item)" class="ml-auto text-red-500 hover:text-red-700">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </label>
        </template>
    </div>
    <div class="flex gap-2">
        <input type="text" x-model="newSw" @keydown.enter.prevent="if(newSw) { software.push(newSw); newSw = '' }" placeholder="Otro software..." 
               class="flex-1 bg-sigma-bg border border-sigma-b border-dashed rounded-xl px-3 py-1.5 text-[10px] uppercase font-bold focus:border-sigma-tx transition-all">
        <button type="button" @click="if(newSw) { software.push(newSw); newSw = '' }"
                class="px-4 py-1.5 bg-sigma-bg2 border border-sigma-b rounded-xl text-[10px] font-black uppercase hover:bg-sigma-tx hover:text-sigma-bg transition-all">
            Añadir
        </button>
    </div>
</div>
