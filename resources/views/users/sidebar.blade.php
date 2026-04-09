<div class="p-4 space-y-6">
    
    {{-- Datos Básicos --}}
    <x-sidebar-section icon="ri-user-line" label="Perfil de Usuario">
        <div class="space-y-4">
            {{-- Nombre --}}
            <div class="relative">
                <label class="text-[10px] uppercase font-black tracking-widest mb-1 block" style="color:var(--tx2)">Nombre completo</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="value" 
                        value="{{ $data->name }}"
                        hx-post="{{ route('users.field.update', $data->id) }}"
                        hx-vals='{"field": "name"}'
                        hx-trigger="change"
                        hx-swap="none"
                        hx-indicator="#name-indicator"
                        class="w-full bg-sigma-bg2 border border-sigma-b rounded-lg px-3 py-2 text-xs font-semibold focus:border-sigma-ac focus:ring-1 focus:ring-sigma-ac transition-all outline-none"
                        style="color:var(--tx)"
                        placeholder="Nombre completo..."
                    >
                    <div id="name-indicator" class="htmx-indicator absolute right-3 top-1/2 -translate-y-1/2">
                        <i class="ri-loader-4-line animate-spin text-sigma-ac text-sm"></i>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="relative">
                <label class="text-[10px] uppercase font-black tracking-widest mb-1 block" style="color:var(--tx2)">Correo Electrónico</label>
                <div class="relative">
                    <input 
                        type="email" 
                        name="value" 
                        value="{{ $data->email }}"
                        hx-post="{{ route('users.field.update', $data->id) }}"
                        hx-vals='{"field": "email"}'
                        hx-trigger="change"
                        hx-swap="none"
                        hx-indicator="#email-indicator"
                        class="w-full bg-sigma-bg2 border border-sigma-b rounded-lg px-3 py-2 text-xs font-semibold focus:border-sigma-ac focus:ring-1 focus:ring-sigma-ac transition-all outline-none"
                        style="color:var(--tx)"
                        placeholder="correo@ejemplo.com"
                    >
                    <div id="email-indicator" class="htmx-indicator absolute right-3 top-1/2 -translate-y-1/2">
                        <i class="ri-loader-4-line animate-spin text-sigma-ac text-sm"></i>
                    </div>
                </div>
            </div>

            <x-sidebar-row label="Documento" :value="$data->document ?? '—'" />
        </div>
    </x-sidebar-section>

    {{-- Estado del Usuario --}}
    <x-sidebar-section icon="ri-toggle-line" label="Configuración">
        <div 
            x-data="{ active: @js($data->isActive) }"
            class="flex items-center justify-between p-3 rounded-xl border border-sigma-b bg-sigma-bg2 shadow-sm transition-all"
        >
            <div class="flex flex-col">
                <span class="text-[10px] font-black uppercase tracking-widest text-sigma-tx">Estado Actual</span>
                <span 
                    class="text-[9px] font-bold uppercase"
                    :class="active ? 'text-green-500' : 'text-red-500'"
                    x-text="active ? 'Activo' : 'Inactivo'"
                >
                </span>
            </div>
            
            <button 
                @click="active = !active"
                hx-post="{{ route('users.status.update', $data->id) }}"
                hx-swap="none"
                hx-indicator="#status-loader"
                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sigma-ac focus-visible:ring-offset-2"
                :class="active ? 'bg-green-500' : 'bg-sigma-b'"
            >
                <span class="sr-only">Cambiar Estado</span>
                <span 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="active ? 'translate-x-5' : 'translate-x-1'"
                ></span>
            </button>
        </div>
        <div id="status-loader" class="htmx-indicator flex justify-center mt-2">
            <i class="ri-loader-4-line animate-spin text-sigma-ac"></i>
        </div>
    </x-sidebar-section>

    {{-- Seguridad --}}
    <x-sidebar-section icon="ri-shield-user-line" label="Seguridad">
        <button 
            hx-post="{{ route('users.password.reset', $data->id) }}"
            hx-swap="none"
            hx-confirm="¿Estás seguro de enviar un correo de restauración a este usuario?"
            class="w-full flex items-center justify-center gap-2 p-3 rounded-xl border border-sigma-b bg-sigma-bg hover:bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-ac transition-all text-[10px] font-bold uppercase"
        >
            <i class="ri-mail-send-line text-sm text-sigma-ac"></i>
            Enviar Reset Password
        </button>
    </x-sidebar-section>

</div>
