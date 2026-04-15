<div class="overflow-auto max-h-[65vh]">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-3" style="color:var(--tx2)">
        <i class="ri-alert-line text-base"></i>
        <span class="uppercase tracking-wider">Riesgos</span>
    </h3>

    @if(count($risks) > 0)
        <div class="space-y-1">
            @foreach($risks as $risk)
                <div class="text-xs px-3 py-1.5 rounded" style="background:var(--bg2); color:var(--tx)">
                    {{ $risk }}
                </div>
            @endforeach
        </div>
    @else
        <p class="text-xs italic" style="color:var(--tx2)">Sin riesgos registrados para esta división.</p>
    @endif
</div>
