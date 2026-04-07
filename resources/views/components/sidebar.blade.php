@php
    $sidebarMenu = app(\App\Actions\Dashboard\LoadSidebar::class)->handle();
    $currentUrl  = request()->url();

    // Helper para detectar si una URL relativa matchea la actual
    $isActive = fn($url) => $url && str_contains($currentUrl, ltrim($url, '?'));
@endphp

<aside id="sidebar"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 w-64 flex flex-col z-50 shrink-0 lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out bg-sigma-bg border-r border-sigma-b h-screen">

    {{-- Logo --}}
    <div class="h-20 flex items-center justify-between px-10 pt-8 shrink-0">
        <a href="{{ url('/home') }}" class="group w-full">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo"
                class="w-full px-6 object-contain transition group-hover:scale-110">
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-2xl text-sigma-tx hover:rotate-90 transition-transform">
            <i class="ri-close-line"></i>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto scroll px-4 py-6 space-y-1">
        @foreach($sidebarMenu as $group)
            @php
                $hasChildren = !empty($group['children']);

                // Recorre todos los niveles para detectar active
                $groupActive = $isActive($group['url']) || collect($group['children'])->contains(function ($child) use ($isActive) {
                    if (!empty($child['children'])) {
                        return collect($child['children'])->contains(fn($sub) => $isActive($sub['url']));
                    }
                    return $isActive($child['url']);
                });
            @endphp

            @if(!$hasChildren)
                {{-- ── CASO 1: Item directo ── --}}
                <a href="{{ $group['url'] ?: '#' }}"
                   @class([
                       'flex items-center gap-3 px-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all',
                       'bg-sigma-ac text-sigma-ac-inv shadow'                                                        => $isActive($group['url']),
                       'text-sigma-tx opacity-50 hover:opacity-100 hover:bg-sigma-bg2 border border-transparent hover:border-sigma-b' => !$isActive($group['url']),
                   ])>
                    {!! $group['icon'] !!}
                    <span>{{ $group['title'] }}</span>
                </a>

            @else
                {{-- ── CASO 2/3: Grupo desplegable ── --}}
                <div x-data="{ open: @js($groupActive) }">
                    <button @click="open=!open"
                            @class([
                                'w-full flex items-center gap-3 px-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all border',
                                'bg-sigma-bg2 text-sigma-tx border-sigma-b'                                                              => $groupActive,
                                'text-sigma-tx opacity-50 border-transparent hover:opacity-100 hover:bg-sigma-bg2 hover:border-sigma-b'  => !$groupActive,
                            ])>
                        {!! $group['icon'] !!}
                        <span class="flex-1 text-left">{{ $group['title'] }}</span>
                        <i class="ri-arrow-right-s-line transition-transform duration-200 text-xs" :class="open ? 'rotate-90' : ''"></i>
                    </button>

                    <div x-show="open" x-transition x-cloak class="mt-1 ml-4 border-l border-sigma-b pl-2 space-y-0.5">
                        @foreach($group['children'] as $child)
                            @php $isSubGroup = !empty($child['children']); @endphp

                            @if($isSubGroup)
                                {{-- ── CASO 3: Sub-grupo desplegable ── --}}
                                @php $subActive = collect($child['children'])->contains(fn($s) => $isActive($s['url'])); @endphp
                                <div x-data="{ s: @js($subActive) }">
                                    <button @click="s=!s"
                                            @class([
                                                'w-full flex items-center px-3 py-2 text-[10px] font-bold uppercase rounded-lg transition-all',
                                                'text-sigma-tx opacity-100'                              => $subActive,
                                                'text-sigma-tx opacity-40 hover:opacity-100 hover:bg-sigma-bg2' => !$subActive,
                                            ])>
                                        <span class="flex-1 text-left">{{ $child['title'] }}</span>
                                        <i class="ri-arrow-down-s-line text-xs transition-transform" :class="s ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="s" x-cloak class="ml-3">
                                        @foreach($child['children'] as $sub)
                                            <a href="{{ $sub['url'] ?: '#' }}"
                                               @class([
                                                   'block py-1.5 px-3 text-[10px] font-mono transition-all',
                                                   'text-sigma-ac font-bold'                                          => $isActive($sub['url']),
                                                   'text-sigma-tx2 opacity-40 hover:opacity-100 hover:translate-x-1'  => !$isActive($sub['url']),
                                               ])>
                                                {{ $sub['title'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                            @else
                                {{-- ── CASO 2: Hijo directo ── --}}
                                <a href="{{ $child['url'] ?: '#' }}"
                                   @class([
                                       'block px-3 py-2 text-[10px] font-bold uppercase rounded-lg transition-all',
                                       'text-sigma-tx bg-sigma-bg2 border border-sigma-b'               => $isActive($child['url']),
                                       'text-sigma-tx2 opacity-40 hover:opacity-100 hover:bg-sigma-bg2' => !$isActive($child['url']),
                                   ])>
                                    {{ $child['title'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>
</aside>