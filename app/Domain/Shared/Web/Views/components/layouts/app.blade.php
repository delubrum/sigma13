<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @cspMetaTag

    <title>SIGMA{{ ($title ?? '') ? " | $title" : '' }}</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        ::view-transition-old(root), ::view-transition-new(root) { animation: none; mix-blend-mode: normal; }
        ::view-transition-old(root) { z-index: 1; }
        ::view-transition-new(root) { z-index: 9999; }

        #modal-panel, #modal-panel-2 {
            transition: max-width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/favicon.png">
</head>
<body class="h-screen w-screen flex overflow-hidden text-sigma-tx font-sans antialiased bg-sigma-bg">
    
    <div class="flex h-full w-full overflow-hidden">
        <x-sidebar />
        <div class="flex-1 flex flex-col min-w-0 h-full relative overflow-hidden">
            <x-navbar :title="$title ?? ''" :icon="$icon ?? ''" />
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 lg:p-10 scroll bg-sigma-bg animate-core">
                <div class="max-w-full mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Overlay móvil --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
         x-cloak>
    </div>
    {{-- MODAL GLOBAL --}}
    <div id="sigma-modal"
         x-data="{ 
            open: false, 
            modalWidth: 'max-w-4xl', 
            icon: 'ri-loader-4-line', 
            title: 'Cargando...', 
            subtitle: 'Por favor espere' 
         }"
         @open-modal.window="open = true; document.getElementById('modal-actions').innerHTML = '';"
         @close-modal.window="open = false"
         @set-modal-width.window="modalWidth = $event.detail.width"
         @update-modal-actions.window="const el = document.getElementById('modal-actions'); el.innerHTML = $event.detail.html; htmx.process(el);"
         @update-modal-header.window="icon = $event.detail.icon; title = $event.detail.title; subtitle = $event.detail.subtitle"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start pt-4 pb-4 justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-500"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    >
        <div id="modal-panel" x-show="open"
            x-transition:enter="animate-bounce-in" x-transition:leave="animate-bounce-out"
            class="w-[98%] max-h-[94vh] rounded-2xl flex flex-col overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative border border-sigma-b"
            :class="modalWidth"
            style="background:var(--bg)"
        >
            <div class="p-4 border-b flex justify-between items-center shrink-0" style="background:var(--bg2); border-bottom:1px solid var(--b)">
                <div class="flex items-center gap-3">
                    <div id="modal-icon" class="p-2 rounded-lg shadow-md" style="background:var(--ac)">
                        <i :class="icon" class="text-xl" style="color:var(--ac-inv)"></i>
                    </div>
                    <div>
                        <h1 id="modal-title" class="text-xl font-extrabold uppercase tracking-tight" style="color:var(--tx)" x-html="title"></h1>
                        <p id="modal-subtitle" class="text-[10px] uppercase font-bold tracking-widest" style="color:var(--tx2); opacity:.6" x-html="subtitle"></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div id="modal-actions"></div>
                    <button onclick="window.dispatchEvent(new CustomEvent('close-modal'))" class="text-3xl transition-transform hover:scale-110 active:scale-95" style="color:var(--tx)">
                        <i class="ri-close-circle-fill"></i>
                    </button>
                </div>
            </div>
            <div id="modal-body" class="p-6 flex flex-col grow overflow-y-auto" style="background:var(--bg)">
                <div class="flex justify-center p-10 opacity-20">
                    <i class="ri-loader-4-line animate-spin text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GLOBAL CASCADA (Nivel 2) --}}
    <div id="sigma-modal-2"
        x-data="{ 
            open: false, 
            modalWidth: 'max-w-2xl',
            icon: 'ri-loader-4-line',
            title: 'Cargando...',
            subtitle: 'Por favor espere'
        }"
        @open-modal-2.window="open = true; document.getElementById('modal-actions-2').innerHTML = '';"
        @close-modal-2.window="open = false"
        @set-modal-width-2.window="modalWidth = $event.detail.width"
        @update-modal-actions-2.window="document.getElementById('modal-actions-2').innerHTML = $event.detail.html"
        @update-modal-header-2.window="icon = $event.detail.icon; title = $event.detail.title; subtitle = $event.detail.subtitle"
        x-show="open" x-cloak
        class="fixed inset-0 z-60 flex items-start pt-4 pb-4 justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-500"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    >
        <div id="modal-panel-2" x-show="open"
            x-transition:enter="animate-bounce-in" x-transition:leave="animate-bounce-out"
            class="w-[98%] max-h-[94vh] rounded-2xl flex flex-col overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative border border-sigma-b"
            :class="modalWidth"
            style="background:var(--bg)"
            @click.stop
        >
            <div class="p-4 border-b flex justify-between items-center shrink-0" style="background:var(--bg2); border-bottom:1px solid var(--b)">
                <div class="flex items-center gap-3">
                    <div id="modal-icon-2" class="p-2 rounded-lg shadow-md" style="background:var(--ac)">
                        <i :class="icon" class="text-xl" style="color:var(--ac-inv)"></i>
                    </div>
                    <div>
                        <h1 id="modal-title-2" class="text-xl font-extrabold uppercase tracking-tight" style="color:var(--tx)" x-html="title"></h1>
                        <p id="modal-subtitle-2" class="text-[10px] uppercase font-bold tracking-widest" style="color:var(--tx2); opacity:.6" x-html="subtitle"></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div id="modal-actions-2"></div>
                    <button onclick="window.dispatchEvent(new CustomEvent('close-modal-2'))" class="text-3xl transition-transform hover:scale-110 active:scale-95" style="color:var(--tx)">
                        <i class="ri-close-circle-fill"></i>
                    </button>
                </div>
            </div>
            <div id="modal-body-2" class="p-6 flex flex-col flex-grow overflow-y-auto" style="background:var(--bg)">
                <div class="flex justify-center p-10 opacity-20">
                    <i class="ri-loader-4-line animate-spin text-4xl"></i>
                </div>
            </div>
        </div>
    </div>



    @stack('scripts')

    {{-- INDICADOR DE CARGA GLOBAL HTMX --}}
    <div id="global-loader" class="htmx-indicator fixed inset-0 z-[200] flex items-center justify-center bg-sigma-bg/20 backdrop-blur-[4px] pointer-events-none transition-all duration-300">
        <div class="flex flex-col items-center gap-4 animate-core">
            <div class="relative flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-indigo-500/10 animate-ping"></div>
                
                <div class="relative w-24 h-24 rounded-[2rem] bg-black border border-white/10 shadow-2xl flex items-center justify-center backdrop-blur-xl">
                    
                    <img src="/images/loader.gif" class="w-16 h-16 object-contain invert">
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>