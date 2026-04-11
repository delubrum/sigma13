<div 
    x-data="{ 
        search: '',
        userPermissions: @js(array_map('intval', $userPermissions)),
        toggle(id) {
            id = parseInt(id);
            if (this.userPermissions.includes(id)) {
                this.userPermissions = this.userPermissions.filter(i => i !== id);
            } else {
                this.userPermissions = [...this.userPermissions, id];
            }
        },
        isVisible(name, title) {
            if (!this.search) return true;
            const s = this.search.toLowerCase();
            return name.toLowerCase().includes(s) || (title && title.toLowerCase().includes(s));
        },
        isCategoryVisible(items) {
            if (!this.search) return true;
            return items.some(i => this.isVisible(i.name, i.title));
        }
    }"
    class="p-4 h-full flex flex-col gap-4 overflow-hidden"
>
    
    {{-- Buscador --}}
    <div class="relative shrink-0">
        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-sigma-tx2 text-sm opacity-50"></i>
        <input 
            type="text" 
            x-model="search"
            placeholder="Buscar permisos por nombre o descripción..."
            class="w-full pl-10 pr-4 py-3 rounded-2xl bg-sigma-bg border border-sigma-b text-[11px] font-bold uppercase tracking-wider focus:ring-2 focus:ring-sigma-ac focus:border-sigma-ac transition-all outline-none"
        >
    </div>

    {{-- Lista Grouped --}}
    <div class="flex-1 overflow-y-auto scroll pr-2 space-y-8">
        @foreach($permissions as $category => $items)
            <div 
                x-show="isCategoryVisible(@js($items))"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="flex flex-col gap-2"
            >
                {{-- Categoría Header --}}
                <div class="flex items-center justify-between sticky top-0 bg-sigma-bg z-10 py-2 border-b border-sigma-b/50">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-sigma-ac"></div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-sigma-tx">
                            {{ $category }}
                        </h3>
                    </div>
                </div>

                {{-- Permisos en Lista --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mt-2">
                    @foreach($items as $permission)
                        <div 
                            x-show="isVisible(@js($permission->name), @js($permission->title))"
                            @click="toggle(@js($permission->id))"
                            hx-post="{{ route('users.permission.update', $user->id) }}"
                            hx-vals='{"permission_id": "{{ $permission->id }}"}'
                            hx-swap="none"
                            class="flex items-center justify-between p-2.5 rounded-xl border border-sigma-b hover:bg-sigma-bg2 cursor-pointer transition-all group select-none"
                            :class="userPermissions.includes({{ (int)$permission->id }}) ? 'bg-sigma-ac/4 border-sigma-ac/30 shadow-sm' : 'bg-sigma-bg'"
                        >
                            <div class="flex items-center min-w-0 pr-2">
                                <span class="text-[10px] font-black text-sigma-tx uppercase truncate" title="{{ $permission->name }}">{{ $permission->name }}</span>
                            </div>

                            {{-- Switch --}}
                            <div class="relative inline-flex h-3.5 w-6 shrink-0 items-center rounded-full transition-colors focus-visible:outline-none"
                                 :class="userPermissions.includes({{ (int)$permission->id }}) ? 'bg-sigma-ac' : 'bg-sigma-b'"
                            >
                                <span 
                                    class="pointer-events-none inline-block h-2.5 w-2.5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="userPermissions.includes({{ (int)$permission->id }}) ? 'translate-x-3' : 'translate-x-0.5'"
                                ></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
