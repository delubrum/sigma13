@if(count($options ?? []) > 0)
    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
        <button @click="open = !open" 
                class="px-2 md:px-4 py-2 rounded-lg flex items-center gap-2 text-[10px] font-black uppercase tracking-widest transition-all hover:scale-102 active:scale-98 shadow-lg border border-sigma-b/50"
                style="background:var(--ac); color:var(--ac-inv); height: 40px;">
            <i class="ri-menu-line text-sm"></i>
            <span class="hidden md:inline">Opciones</span>
            <i class="ri-arrow-down-s-line transition-transform duration-200 hidden md:inline" :class="open ? 'rotate-180' : ''"></i>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-cloak
             class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl z-50 overflow-hidden border border-sigma-b animate-core" 
             style="background:var(--bg); display: none;">
            <div class="py-1">
                @foreach($options as $option)
                    @php
                        $finalUrl = str_starts_with($option->route, '/') ? $option->route : '/' . $option->route;
                        if (isset($id)) $finalUrl .= '/' . $id;
                        $openEvent = $option->level > 1 ? "window.dispatchEvent(new CustomEvent('open-modal-{$option->level}'))" : "window.dispatchEvent(new CustomEvent('open-modal'))";
                    @endphp
                    <a href="javascript:void(0)" 
                       hx-{{ strtolower($option->method) }}="{{ $finalUrl }}" 
                       hx-target="{{ $option->target }}"
                       hx-swap="innerHTML"
                       @if($option->confirm) hx-confirm="{{ $option->confirm }}" @endif
                       @if($option->prompt)
                         hx-on:htmx:config-request="
                            const val = prompt('{{ $option->prompt }}');
                            if(!val) return event.preventDefault();
                            event.detail.parameters['prompt_value'] = val;
                         "
                       @endif
                       @click="open = false; {{ $openEvent }}"
                       class="group flex items-center px-4 py-2.5 text-[11px] font-bold uppercase tracking-tight transition-all hover:bg-sigma-bg2"
                       style="color:var(--tx2)">
                        <i class="{{ $option->icon }} mr-3 text-lg transition-transform group-hover:scale-110" style="color:var(--ac)"></i>
                        <span class="group-hover:text-sigma-tx">{{ $option->label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
