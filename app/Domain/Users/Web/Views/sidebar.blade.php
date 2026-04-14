<div class="p-4 space-y-6">
    
    {{-- Datos Básicos --}}
    <x-sidebar-section icon="ri-user-line" label="Perfil de Usuario">
        <div class="space-y-4">
            @foreach($data->fields as $field)
                @if(in_array($field->name, ['name', 'email', 'document']))
                    <x-form-field 
                        :field="$field" 
                        :value="old($field->name, $data->{$field->name === 'is_active' ? 'isActive' : $field->name} ?? null)"
                        :hx-post="route('users.field.update', $data->id)"
                        :hx-vals="['field' => $field->name]"
                        hx-trigger="change"
                    />
                @endif
            @endforeach
        </div>
    </x-sidebar-section>

    {{-- Estado del Usuario --}}
    <x-sidebar-section icon="ri-toggle-line" label="Configuración">
        @php 
            $isActiveField = collect($data->fields)->firstWhere('name', 'is_active');
        @endphp
        @if($isActiveField)
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
        @endif
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
