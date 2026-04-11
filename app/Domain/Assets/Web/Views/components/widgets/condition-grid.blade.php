<div class="space-y-2 w-full">
    @php
        $hwItems = [
            'Pantalla' => 'Screen', 'Teclado' => 'Keyboard', 'Batería' => 'Battery', 
            'Disco Duro' => 'Hdd', 'Procesador' => 'Processor', 'Memoria RAM' => 'Ram', 
            'Cargador' => 'Charger', 'Carcasa/Maletín' => 'Case'
        ];
    @endphp
    @foreach($hwItems as $label => $key)
        <div class="flex items-center justify-between p-3 rounded-xl border border-sigma-b bg-sigma-bg2/30 group hover:bg-sigma-bg2 transition-all">
            <span class="text-xs font-bold text-sigma-tx2 uppercase">{{ $label }}</span>
            <div class="flex gap-2">
                <label class="relative flex items-center cursor-pointer group/radio">
                    <input type="radio" name="hardware[{{ $label }}]" value="Good" required class="peer sr-only">
                    <span class="px-3 py-1 bg-sigma-bg border border-sigma-b rounded-lg text-[10px] font-bold uppercase transition-all peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 hover:border-sigma-tx">Bueno</span>
                </label>
                <label class="relative flex items-center cursor-pointer group/radio">
                    <input type="radio" name="hardware[{{ $label }}]" value="Bad" class="peer sr-only">
                    <span class="px-3 py-1 bg-sigma-bg border border-sigma-b rounded-lg text-[10px] font-bold uppercase transition-all peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 hover:border-sigma-tx">Malo</span>
                </label>
            </div>
        </div>
    @endforeach
</div>
