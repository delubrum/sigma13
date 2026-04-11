<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <title>{{ config('app.name', 'SIGMA') }} - Nueva Contraseña</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
        @cspNonce
    </head>
    <body class="bg-white text-neutral-900 font-sans antialiased">
        
        <div class="min-h-screen flex items-center justify-center px-6 py-12"
             x-data="{ loading: false }"
             @htmx:after-request="loading = false">
             
            <div class="w-full max-w-sm">
                
                <div class="text-center mb-16">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SIGMA" class="h-14 w-auto mx-auto">
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400 mt-4">Establecer Nueva Contraseña</p>
                </div>

                <form 
                    hx-post="{{ route('password.update') }}"
                    hx-swap="none"
                    @submit="loading = true"
                    class="flex flex-col"
                >
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">

                    <div class="group flex flex-col mb-10" x-data="{ show: false }">
                        <label for="password" class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2 ml-1 transition-colors group-focus-within:text-neutral-900">
                            Nueva contraseña
                        </label>
                        <div class="flex items-center border-b-[1.5px] border-neutral-200 py-3 transition-colors group-focus-within:border-neutral-900">
                            <button type="button" @click="show = !show" class="cursor-pointer text-neutral-400 hover:text-neutral-900 transition-colors focus:outline-none">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password" id="password"
                                autocomplete="new-password"
                                placeholder="••••••••" required autofocus
                                class="flex-1 bg-transparent border-none pl-4 text-base focus:ring-0 focus:outline-none placeholder-neutral-300"
                            >
                        </div>
                    </div>

                    <div class="group flex flex-col mb-12" x-data="{ show: false }">
                        <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2 ml-1 transition-colors group-focus-within:text-neutral-900">
                            Confirmar contraseña
                        </label>
                        <div class="flex items-center border-b-[1.5px] border-neutral-200 py-3 transition-colors group-focus-within:border-neutral-900">
                            <button type="button" @click="show = !show" class="cursor-pointer text-neutral-400 hover:text-neutral-900 transition-colors focus:outline-none">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password_confirmation" id="password_confirmation"
                                autocomplete="new-password"
                                placeholder="••••••••" required
                                class="flex-1 bg-transparent border-none pl-4 text-base focus:ring-0 focus:outline-none placeholder-neutral-300"
                            >
                        </div>
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="cursor-pointer w-full py-5 bg-neutral-900 text-white font-bold text-[11px] tracking-[0.3em] hover:bg-neutral-800 transition-all shadow-sm active:scale-[0.98] disabled:opacity-70"
                            :disabled="loading"
                        >
                            <span class="flex items-center justify-center gap-3">
                                <span x-show="!loading">GUARDAR CAMBIOS</span>
                                <span x-show="loading" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    PROCESANDO...
                                </span>
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>