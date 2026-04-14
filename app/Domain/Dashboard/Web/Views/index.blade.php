{{-- resources/views/home.blade.php --}}
<x-layouts.app>
    @if(!auth()->user()->telegram_chat_id)
        <div class="max-w-4xl mx-auto mt-6">
            <div class="bg-blue-600 rounded-xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl">
                        <i class="ri-telegram-fill"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-white">Activa tus notificaciones</h3>
                        <p class="text-blue-100 text-sm">Vincula tu cuenta de Telegram para recibir alertas en tiempo real.</p>
                    </div>
                </div>
                <a href="https://t.me/{{ config('services.telegram.bot_name') }}?start={{ auth()->user()->telegram_link_token }}" 
                   target="_blank" 
                   class="bg-white text-blue-600 px-6 py-2.5 rounded-lg font-bold hover:bg-blue-50 transition-colors flex items-center gap-2">
                    <i class="ri-link"></i>
                    Vincular ahora
                </a>
            </div>
        </div>
    @endif
    
    <section class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center">
            <h1 class="text-4xl font-black uppercase tracking-tight" style="color:var(--tw-tx)">
                Bienvenido
            </h1>
            <p class="mt-2 text-sm font-mono" style="color:var(--tw-tx2)">
                Selecciona un módulo del menú para comenzar
            </p>
        </div>
    </section>

</x-layouts.app>