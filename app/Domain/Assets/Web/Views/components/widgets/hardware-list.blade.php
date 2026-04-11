<div x-data="{ hardware: @js($data['hardware'] ?? []), newHw: '' }" class="w-full">
    <div class="grid grid-cols-2 gap-2 mb-3">
        @php $baseHw = ['Base', 'Teclado', 'Mouse', 'Auriculares', 'Webcam']; @endphp
        @foreach($baseHw as $hw)
            <label class="flex items-center gap-3 p-3 rounded-xl border border-sigma-b bg-sigma-bg2/50 hover:bg-sigma-bg2 cursor-pointer transition-all group">
                <input type="checkbox" name="hardware[]" value="{{ $hw }}" 
                       {{ in_array($hw, $data['hardware'] ?? []) ? 'checked' : '' }}
                       class="rounded border-sigma-b text-sigma-ac focus:ring-sigma-ac/20 w-4 h-4 bg-sigma-bg">
                <span class="text-xs font-bold text-sigma-tx2 group-hover:text-sigma-tx uppercase">{{ $hw }}</span>
            </label>
        @endforeach
        <template x-for="(item, index) in hardware.filter(i => !@js($baseHw).includes(i))" :key="index">
            <label class="flex items-center gap-3 p-3 rounded-xl border border-sigma-ac/30 bg-sigma-bg2 hover:bg-sigma-bg2 cursor-pointer transition-all group animate-core">
                <input type="checkbox" name="hardware[]" :value="item" checked
                       class="rounded border-sigma-b text-sigma-ac focus:ring-sigma-ac/20 w-4 h-4 bg-sigma-bg">
                <span class="text-xs font-bold text-sigma-tx uppercase" x-text="item"></span>
                <button type="button" @click="hardware = hardware.filter(i => i !== item)" class="ml-auto text-red-500 hover:text-red-700">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </label>
        </template>
    </div>
    <div class="flex gap-2">
        <input type="text" x-model="newHw" @keydown.enter.prevent="if(newHw) { hardware.push(newHw); newHw = '' }" placeholder="Otro hardware..." 
               class="flex-1 bg-sigma-bg border border-sigma-b border-dashed rounded-xl px-3 py-1.5 text-[10px] uppercase font-bold focus:border-sigma-tx transition-all">
        <button type="button" @click="if(newHw) { hardware.push(newHw); newHw = '' }"
                class="px-4 py-1.5 bg-sigma-bg2 border border-sigma-b rounded-xl text-[10px] font-black uppercase hover:bg-sigma-tx hover:text-sigma-bg transition-all">
            Añadir
        </button>
    </div>
</div>
