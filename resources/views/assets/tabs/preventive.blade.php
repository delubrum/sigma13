<div class="rounded-lg shadow-sm border overflow-hidden" style="background:var(--bg); border-color:var(--b)">
    <div class="px-3 py-1.5 border-b flex justify-between items-center" style="background:var(--bg2); border-color:var(--b)">
        <span class="text-[11px] font-black uppercase tracking-widest" style="color:var(--tx)">Cronograma Maestro {{ date('Y') }}</span>
        <div class="flex gap-2">
            <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full opacity-50" style="background:var(--tx2)"></div><span class="text-[8px] font-bold" style="color:var(--tx2)">PASADO</span></div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full" style="background:var(--ac)"></div><span class="text-[8px] font-bold uppercase" style="color:var(--ac)">PENDIENTE</span></div>
        </div>
    </div>

    <div class="overflow-x-auto custom-scroll">
        <table class="min-w-[1900px] w-full table-fixed border-separate border-spacing-0">
            <thead>
                <tr class="text-white" style="background:var(--tx)">
                    <th class="sticky left-0 z-50 w-[250px] p-2 border-r text-left text-[9px] uppercase font-black" style="background:var(--tx); border-color:var(--bg2)">Actividad / Frecuencia</th>
                    @php
                        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    @endphp
                    @foreach ($meses as $m)
                        <th colspan="4" class="text-[9px] border-r py-1 uppercase font-black text-center" style="border-color:var(--bg2)">{{ $m }}</th>
                    @endforeach
                    <th colspan="4" style="background:var(--tx2)"></th>
                </tr>
                <tr>
                    <th class="sticky left-0 z-50 border-r border-b p-1" style="background:var(--bg2); border-color:var(--b)"></th>
                    @php $semanaActual = (int) date('W'); @endphp
                    @for ($i = 1; $i <= 52; $i++)
                        <th class="w-[30px] text-[8px] font-black border-r border-b py-0.5 {{ $i === $semanaActual ? 'text-black' : '' }}" 
                            style="border-color:var(--b); background:{{ $i === $semanaActual ? '#facc15' : 'var(--bg2)' }}; color:{{ $i === $semanaActual ? '#000' : 'var(--tx2)' }}">
                            {{ $i }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($automations as $task)
                <tr class="transition-colors hover:opacity-80">
                    <td class="sticky left-0 z-40 border-r border-b p-2 whitespace-nowrap overflow-hidden" style="background:var(--bg); border-color:var(--b); box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1)">
                        <div class="text-[10px] font-bold uppercase truncate" style="color:var(--tx)">{{ $task->activity }}</div>
                        <div class="text-[7px] font-black uppercase italic leading-none mt-0.5" style="color:var(--ac)">{{ $task->frequency }}</div>
                    </td>

                    @for ($s = 1; $s <= 52; $s++)
                        @php
                            $esMantenimiento = ($task->intervalo_semanas > 0) && (($s - $task->semana_referencia) % $task->intervalo_semanas === 0);
                            $colorBg = '';
                            $colorText = '';
                            
                            if ($esMantenimiento) {
                                if ($s < $semanaActual) {
                                    $colorBg = 'var(--tx2)';
                                    $colorText = 'var(--bg)';
                                } elseif ($s === $semanaActual) {
                                    $colorBg = '#eab308'; // amarillo amber-500
                                    $colorText = '#fff';
                                } else {
                                    if ($task->is_vencido) {
                                        $colorBg = '#ef4444'; // red-500
                                    } else {
                                        $colorBg = 'var(--ac)'; // action color
                                    }
                                    $colorText = 'var(--ac-inv)';
                                }
                            }
                        @endphp
                        <td class="w-[30px] h-7 border-r border-b relative p-0" style="border-color:var(--b)">
                            @if ($esMantenimiento)
                                <div class="absolute inset-0 flex items-center justify-center p-[2px]">
                                    <div class="w-full h-[18px] rounded-sm flex items-center justify-center text-[8px] font-black {{ $s === $semanaActual ? 'ring-1 ring-amber-700 animate-pulse' : '' }}" 
                                         style="background:{{ $colorBg }}; color:{{ $colorText }}" 
                                         title="S{{ $s }}">
                                        {!! ($s < $semanaActual) ? '&check;' : '' !!}
                                    </div>
                                </div>
                            @endif

                            @if ($s === $semanaActual)
                                <div class="absolute inset-y-0 left-0 w-[1px] z-10 pointer-events-none" style="background:var(--ac); opacity:0.3"></div>
                            @endif
                        </td>
                    @endfor
                </tr>
                @empty
                <tr>
                    <td colspan="53" class="text-center py-8 text-xs font-medium" style="color:var(--tx2)">
                        No hay mantenimientos preventivos programados para este activo.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
