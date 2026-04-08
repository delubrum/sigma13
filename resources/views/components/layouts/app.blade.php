<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        sidebarOpen: false, 
        isDark: false,
        init() {
            this.isDark = localStorage.getItem('theme') === 'dark' || 
                         (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        }
      }"
      :class="{ 'dark': isDark }">
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
    </style>
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
    <div
        id="sigma-modal"
        x-data="{ 
            open: false, 
            modalWidth: '50%',
            icon: 'ri-loader-4-line',
            title: 'Cargando...',
            subtitle: 'Por favor espere'
        }"
        @open-modal.window="open = true"
        @close-modal.window="open = false"
        @set-modal-width.window="modalWidth = $event.detail.width"
        @update-modal-header.window="icon = $event.detail.icon; title = $event.detail.title; subtitle = $event.detail.subtitle"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-start pt-4 justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-500"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            id="modal-panel"
            x-show="open"
            x-transition:enter="animate-bounce-in"
            x-transition:leave="animate-bounce-out"
            :class="'w-[95%] max-h-[98vh] sm:w-[' + modalWidth + '] rounded-2xl flex flex-col overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative border border-sigma-b'"
            style="background:var(--bg)"
        >
            {{-- Header --}}
            <div class="p-4 border-b flex justify-between items-center shrink-0"
                 style="background:var(--bg2); border-bottom:1px solid var(--b)">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg shadow-md" style="background:var(--ac)">
                        <i :class="icon + ' text-xl'" style="color:var(--ac-inv)"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold uppercase tracking-tight"
                            style="color:var(--tx)"
                            x-text="title">
                        </h1>
                        <p class="text-[10px] uppercase font-bold tracking-widest"
                           style="color:var(--tx2); opacity:.6"
                           x-text="subtitle">
                        </p>
                    </div>
                </div>
                <button onclick="window.dispatchEvent(new CustomEvent('close-modal'))"
                        class="text-3xl" style="color:var(--tx)">
                    <i class="ri-close-circle-fill"></i>
                </button>
            </div>

            {{-- Body --}}
            <div id="modal-body"
                 class="p-6 flex flex-col grow overflow-y-auto"
                 style="background:var(--bg)">
                <div class="flex justify-center p-10 opacity-20">
                    <i class="ri-loader-4-line animate-spin text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GLOBAL CASCADA (Nivel 2) --}}
    <div
        id="sigma-modal-2"
        x-data="{ 
            open: false, 
            modalWidth: '50%',
            icon: 'ri-loader-4-line',
            title: 'Cargando...',
            subtitle: 'Por favor espere'
        }"
        @open-modal-2.window="open = true"
        @close-modal-2.window="open = false"
        @set-modal-width-2.window="modalWidth = $event.detail.width"
        @update-modal-header-2.window="icon = $event.detail.icon; title = $event.detail.title; subtitle = $event.detail.subtitle"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-60 flex items-start pt-10 justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-500"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            id="modal-panel-2"
            x-show="open"
            x-transition:enter="animate-bounce-in"
            x-transition:leave="animate-bounce-out"
            :class="'w-[95%] max-h-[95vh] sm:w-[' + modalWidth + '] rounded-2xl flex flex-col overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative border border-sigma-b'"
            style="background:var(--bg)"
            @click.stop
        >
            {{-- Header --}}
            <div class="p-4 border-b flex justify-between items-center shrink-0"
                 style="background:var(--bg2); border-bottom:1px solid var(--b)">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg shadow-md" style="background:var(--ac)">
                        <i :class="icon + ' text-xl'" style="color:var(--ac-inv)"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold uppercase tracking-tight"
                            style="color:var(--tx)"
                            x-text="title">
                        </h1>
                        <p class="text-[10px] uppercase font-bold tracking-widest"
                           style="color:var(--tx2); opacity:.6"
                           x-text="subtitle">
                        </p>
                    </div>
                </div>
                <button onclick="window.dispatchEvent(new CustomEvent('close-modal-2'))"
                        class="text-3xl" style="color:var(--tx)">
                    <i class="ri-close-circle-fill"></i>
                </button>
            </div>

            {{-- Body --}}
            <div id="modal-body-2"
                 class="p-6 flex flex-col flex-grow overflow-y-auto"
                 style="background:var(--bg)">
                <div class="flex justify-center p-10 opacity-20">
                    <i class="ri-loader-4-line animate-spin text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.toggleTheme = async (event) => {
            const root = document.documentElement;
            const alpine = Alpine.$data(root);
            const x = event?.clientX ?? window.innerWidth / 2;
            const y = event?.clientY ?? window.innerHeight / 2;
            const endRadius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));

            if (!document.startViewTransition) {
                alpine.isDark = !alpine.isDark;
                localStorage.setItem('theme', alpine.isDark ? 'dark' : 'light');
                return;
            }

            const transition = document.startViewTransition(() => {
                alpine.isDark = !alpine.isDark;
                localStorage.setItem('theme', alpine.isDark ? 'dark' : 'light');
            });

            await transition.ready;
            root.animate(
                { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`] },
                { duration: 500, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)' }
            );
        };
    </script>

    @stack('scripts')
</body>
</html>