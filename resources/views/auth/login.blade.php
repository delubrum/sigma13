<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <title>{{ config('app.name', 'SIGMA') }} - Acceso</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="bg-white text-neutral-900 font-sans antialiased">
        
        <div class="min-h-screen flex items-center justify-center px-6 py-12" 
             x-data="{ 
                view: '{{ Route::currentRouteName() === 'password.request' ? 'forgot' : 'login' }}', 
                loading: false 
             }"
             @htmx:after-request="loading = false">
            
            <div class="w-full max-w-sm">
                
                <div class="text-center mb-16">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SIGMA" class="h-14 w-auto mx-auto">
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400 mt-4" 
                       x-text="view === 'login' ? 'Acceso al Sistema' : 'Recuperar Contraseña'"></p>
                </div>

                <div class="relative overflow-hidden min-h-[400px]">
                    
                    <div x-show="view === 'login'"
                         x-transition:enter="transition ease-out duration-500 delay-200"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 absolute w-full"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-12">
                        
                        <form hx-post="{{ route('login.store') }}" 
                              hx-swap="none"
                              @submit="loading = true" 
                              class="space-y-4">
                            @csrf
                            @honeypot

                            <div class="group flex flex-col mb-10">
                                <label for="email" class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 ml-1 transition-colors group-focus-within:text-neutral-900">
                                    Correo electrónico
                                </label>
                                <div class="flex items-center border-b-[1.5px] border-neutral-200 py-3 transition-colors group-focus-within:border-neutral-900">
                                    <svg class="w-5 h-5 text-neutral-400 transition-colors group-focus-within:text-neutral-900" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus 
                                           autocomplete="username"
                                           class="flex-1 bg-transparent border-none pl-4 text-base focus:ring-0 focus:outline-none placeholder-neutral-300" 
                                           placeholder="tu@correo.com">
                                </div>
                            </div>

                            <div class="group flex flex-col mb-10" x-data="{ show: false }">
                                <label for="password" class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 ml-1 transition-colors group-focus-within:text-neutral-900">
                                    Contraseña
                                </label>
                                <div class="flex items-center border-b-[1.5px] border-neutral-200 py-3 transition-colors group-focus-within:border-neutral-900">
                                    <button type="button" @click="show = !show" class="cursor-pointer text-neutral-400 hover:text-neutral-900 focus:outline-none">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                    <input :type="show ? 'text' : 'password'" name="password" id="password" required 
                                           autocomplete="current-password"
                                           class="flex-1 bg-transparent border-none pl-4 text-base focus:ring-0 focus:outline-none placeholder-neutral-300" 
                                           placeholder="••••••••">
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] px-1 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer text-neutral-500 hover:text-neutral-800">
                                    <input type="checkbox" name="remember" class="w-4 h-4 accent-neutral-900 border-neutral-300 rounded-sm focus:ring-0 cursor-pointer">
                                    <span>Recordarme</span>
                                </label>
                                <button type="button" @click="view = 'forgot'" class="cursor-pointer text-neutral-400 hover:text-neutral-900 transition-colors underline-offset-4 hover:underline">
                                    ¿Olvidaste tu contraseña?
                                </button>
                            </div>

                            <div class="pt-4">
                                <button type="submit" :disabled="loading" class="cursor-pointer w-full py-5 bg-neutral-900 text-white font-bold text-[11px] tracking-[0.3em] hover:bg-neutral-800 transition-all active:scale-[0.98] disabled:opacity-70">
                                    <span x-show="!loading">ENTRAR</span>
                                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-2 text-white">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        PROCESANDO
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="view === 'forgot'" x-cloak
                         x-transition:enter="transition ease-out duration-500 delay-200"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 absolute w-full"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-12">
                        
                        <div class="text-center mb-8 px-4">
                            <p class="text-sm text-neutral-500 italic">Enviaremos un enlace para restablecer tu contraseña a tu correo electrónico.</p>
                        </div>

                        <form hx-post="{{ route('password.email') }}" 
                              hx-swap="none"
                              @submit="loading = true" 
                              class="space-y-8">
                            @csrf
                            @honeypot

                            <div class="group flex flex-col">
                                <label for="email_recovery" class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 ml-1 transition-colors group-focus-within:text-neutral-900">
                                    Correo electrónico
                                </label>
                                <div class="flex items-center border-b-[1.5px] border-neutral-200 py-3 transition-colors group-focus-within:border-neutral-900">
                                    <svg class="w-5 h-5 text-neutral-400 transition-colors group-focus-within:text-neutral-900" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <input type="email" name="email" id="email_recovery" required 
                                           autocomplete="email"
                                           class="flex-1 bg-transparent border-none pl-4 text-base focus:ring-0 focus:outline-none placeholder-neutral-300" 
                                           placeholder="tu@correo.com">
                                </div>
                            </div>

                            <div class="space-y-6">
                                <button type="submit" :disabled="loading" class="cursor-pointer w-full py-5 bg-neutral-900 text-white font-bold text-[11px] tracking-[0.3em] hover:bg-neutral-800 transition-all active:scale-[0.98] disabled:opacity-70">
                                    <span x-show="!loading">ENVIAR ENLACE</span>
                                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-2 text-white">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        ENVIANDO
                                    </span>
                                </button>

                                <button type="button" @click="view = 'login'" class="w-full text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 hover:text-neutral-900 transition-colors">
                                    ← Volver al inicio de sesión
                                </button>
                            </div>
                        </form>
                    </div>

                </div> 
            </div>
        </div>
    </body>
</html>