@props(['title' => '', 'icon' => ''])

<header class="h-16 flex items-center justify-between px-8 z-40 shrink-0 border-b border-sigma-b bg-sigma-bg select-none">
    <div class="flex items-center space-x-6">
        {{-- Toggle Sidebar (Mobile) --}}
        <button @click="sidebarOpen = true" 
                class="lg:hidden p-2 text-xl text-sigma-tx hover:bg-sigma-bg2 rounded-lg transition-all active:scale-90">
            <i class="ri-menu-2-line"></i>
        </button>

        {{-- Títulos con Look Industrial --}}
        <div class="flex items-baseline space-x-2">
            <div class="flex items-baseline space-x-1.5">
                @if($icon)
                <i class="{{ $icon }} text-lg text-sigma-ac"></i>
                @endif
                <h1 class="text-lg font-black italic tracking-tighter uppercase text-sigma-tx">{{ $title }}</h1>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-3">
        {{-- Theme Switcher con Animación de Iconos Nativa --}}
        <button x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                x-init="window.addEventListener('theme-changed', (e) => isDark = e.detail.isDark)"
                @click="window.toggleTheme($event); isDark = document.documentElement.classList.contains('dark')"
                class="group relative w-9 h-9 rounded-xl flex items-center justify-center overflow-hidden transition-all bg-sigma-bg2 border border-sigma-b hover:border-sigma-ac active:scale-95">
            
            {{-- Sol --}}
            <div class="absolute inset-0 flex items-center justify-center transition-all duration-500 ease-in-out"
                :class="isDark ? 'opacity-0 rotate-180 scale-50' : 'opacity-100 rotate-0 scale-100'">
                <i class="ri-sun-fill text-amber-500 text-lg"></i>
            </div>
            
            {{-- Luna --}}
            <div class="absolute inset-0 flex items-center justify-center transition-all duration-500 ease-in-out"
                :class="isDark ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-180 scale-50'">
                <i class="ri-moon-clear-fill text-indigo-400 text-lg"></i>
            </div>
        </button>

        {{-- Separador Visual --}}
        <div class="h-6 w-[1px] bg-sigma-b mx-1"></div>

        {{-- Notificaciones --}}
        <button class="p-2 text-xl text-sigma-tx2 hover:text-sigma-tx transition-colors relative group"
                onclick="window.notyf?.success('Logs del sistema sincronizados')">
            <i class="ri-notification-3-line group-hover:animate-pulse"></i>
            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-sigma-ac rounded-full border-2 border-sigma-bg"></span>
        </button>

        {{-- Perfil Dropdown --}}
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open"
                    class="flex items-center space-x-2 pl-1 pr-3 py-1 rounded-xl transition-all border border-sigma-b bg-sigma-bg2 hover:border-sigma-tx group">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black uppercase bg-sigma-ac text-sigma-ac-inv group-hover:scale-105 transition-transform">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="hidden md:block text-left mr-1">
                    <p class="text-[9px] font-black uppercase leading-none text-sigma-tx">User_Root</p>
                    <p class="text-[7px] font-mono opacity-40 leading-none mt-1 text-sigma-tx2">Online</p>
                </div>
                <i class="ri-arrow-down-s-line text-[10px] text-sigma-tx transition-transform duration-300" :class="open ? 'rotate-180' : 'opacity-40'"></i>
            </button>

            {{-- Dropdown Menu (Z-index alto para pisar el main) --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-cloak
                 class="absolute right-0 mt-3 w-56 rounded-xl shadow-2xl border border-sigma-b bg-sigma-bg z-[70] overflow-hidden animate-core">
                
                <div class="p-4 bg-sigma-bg2/50 border-b border-sigma-b">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-sigma-b flex items-center justify-center text-lg font-black text-sigma-tx">
                             {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black uppercase text-sigma-tx truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[8px] font-mono text-sigma-tx2 truncate opacity-60 italic">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-2 space-y-1">
                    <a href="#" class="flex items-center space-x-2 px-3 py-2 text-[9px] font-bold uppercase text-sigma-tx2 hover:text-sigma-tx hover:bg-sigma-bg2 rounded-lg transition-all">
                        <i class="ri-user-settings-line text-xs"></i>
                        <span>Configuración_Perfil</span>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-[9px] font-black uppercase text-red-500 hover:bg-red-500/10 rounded-lg flex items-center space-x-2 transition-all group">
                            <i class="ri-power-flash-line text-xs group-hover:rotate-12 transition-transform"></i>
                            <span>Desconectar_Sesión</span>
                        </button>
                    </form>
                </div>

                <div class="bg-sigma-bg2 p-2 flex justify-center">
                    <span class="text-[7px] font-mono opacity-30 tracking-widest uppercase">Sigma_Auth_Token_Verified</span>
                </div>
            </div>
        </div>
    </div>
</header>