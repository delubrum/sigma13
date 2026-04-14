<div class="p-6 flex flex-col items-center justify-center h-full text-center space-y-6">
    <div class="w-24 h-24 rounded-full bg-sigma-ac/10 flex items-center justify-center">
        <i class="ri-user-smile-line text-5xl text-sigma-ac"></i>
    </div>

    <div class="space-y-2">
        <h2 class="text-xl font-black uppercase tracking-tight text-sigma-tx">
            {{ $user->name }}
        </h2>
        <p class="text-xs font-bold uppercase tracking-widest text-sigma-tx2 opacity-60">
            {{ $user->email }}
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 w-full max-w-sm pt-6 border-t border-sigma-b">
        <div class="p-4 rounded-2xl bg-sigma-bg2 border border-sigma-b">
            <span class="text-[9px] font-black uppercase tracking-widest text-sigma-tx2 mb-1 block">Estado</span>
            <span class="text-[10px] font-bold uppercase {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}">
                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
        <div class="p-4 rounded-2xl bg-sigma-bg2 border border-sigma-b">
            <span class="text-[9px] font-black uppercase tracking-widest text-sigma-tx2 mb-1 block">Documento</span>
            <span class="text-[10px] font-bold uppercase text-sigma-tx">
                {{ $user->document ?: 'No definido' }}
            </span>
        </div>
    </div>

    <div class="pt-8">
        <p class="text-[10px] font-bold uppercase tracking-widest text-sigma-tx2 opacity-40 max-w-xs">
            Utilice la pestaña de permisos para gestionar los accesos a los módulos del sistema.
        </p>
    </div>
</div>
