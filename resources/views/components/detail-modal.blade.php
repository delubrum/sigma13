<div class="grid grid-cols-4 gap-4">

    <div id="sidebar-summary" class="rounded-lg shadow-md lg:col-span-1 overflow-hidden" style="background:var(--bg); border:1px solid var(--b)">
        @if($sidebarView && $sidebarData)
            @include($sidebarView, ['data' => $sidebarData])
        @endif
    </div>  

    <div class="rounded-lg shadow-md overflow-hidden lg:col-span-3" style="background:var(--bg); border:1px solid var(--b)">

        @if(count($tabs) > 1)
        {{-- Tab bar --}}
        <div class="flex border-b flex-wrap shrink-0" style="border-color:var(--b); background:var(--bg2)">
            @foreach($tabs as $tab)
            <div class="tab px-3 py-2.5 cursor-pointer font-medium text-sm whitespace-nowrap transition-colors duration-200"
                style="color:var(--tx2)"
                hx-get="{{ route($tab->route, ['id' => $id]) }}"
                hx-target="#tab-content"
                hx-swap="innerHTML"
                data-tab="{{ $tab->key }}"
                @if($tab->default) hx-trigger="load, click" @else hx-trigger="click" @endif>
                <i class="{{ $tab->icon }}"></i> {{ $tab->label }}
            </div>
            @endforeach
        </div>

        {{-- Tab content --}}
        <div id="tab-content" class="p-4 overflow-y-auto flex-grow" style="background:var(--bg)">
            <div class="flex justify-center p-10 opacity-20">
                <i class="ri-loader-4-line animate-spin text-4xl"></i>
            </div>
        </div>
        @elseif(count($tabs) === 1)
        {{-- Single tab - load content directly --}}
        @php $singleTab = $tabs->first(); @endphp
        <div id="tab-content" class="p-4 overflow-y-auto flex-grow" style="background:var(--bg)"
            hx-get="{{ route($singleTab->route, ['id' => $id]) }}"
            hx-trigger="load"
            hx-swap="innerHTML">
            <div class="flex justify-center p-10 opacity-20">
                <i class="ri-loader-4-line animate-spin text-4xl"></i>
            </div>
        </div>
        @endif
    </div>
</div>
