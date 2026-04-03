<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SIGMA') }} - Nueva Contraseña</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @cspNonce
    </head>
    <body class="bg-neutral-100 text-neutral-900 font-sans antialiased">
        <div id="hx-indicator" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden">
            <div class="flex items-center gap-2 bg-neutral-800 text-white px-4 py-2 rounded-full shadow-lg text-sm">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Cargando...</span>
            </div>
        </div>
        <div class="min-h-screen flex items-center justify-center p-6">
            <div class="w-full max-w-sm space-y-8">
                <div class="text-center space-y-2">
                    <h1 class="text-5xl font-bold tracking-tighter text-neutral-900">SIGMA</h1>
                    <p class="text-neutral-500 text-sm">Nueva contraseña</p>
                </div>

                <form 
                    method="POST" 
                    action="{{ route('password.update') }}"
                    class="space-y-6"
                    x-data="{ loading: false }"
                    hx-post="{{ route('password.update') }}"
                    hx-swap="none"
                    @submit="loading = true"
                >
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">

                    <div class="space-y-2 relative" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-neutral-700">Nueva contraseña</label>
                        <input 
                            :type="show ? 'text' : 'password'" 
                            name="password" 
                            id="password"
                            placeholder="••••••••"
                            required
                            minlength="8"
                            class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-lg text-base placeholder-neutral-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 transition-colors pr-12"
                        >
                        <button type="button" @click="show = !show" class="absolute right-3 top-[38px] text-neutral-500 hover:text-neutral-900 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 relative" x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-medium text-neutral-700">Confirmar contraseña</label>
                        <input 
                            :type="show ? 'text' : 'password'" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            placeholder="••••••••"
                            required
                            class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-lg text-base placeholder-neutral-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 transition-colors pr-12"
                        >
                        <button type="button" @click="show = !show" class="absolute right-3 top-[38px] text-neutral-500 hover:text-neutral-900 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>

                    <button 
                        type="submit"
                        class="w-full py-4 bg-neutral-900 text-white font-medium text-sm tracking-wide hover:bg-neutral-800 transition-colors rounded-lg"
                        :disabled="loading"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <span>GUARDAR</span>
                            <svg x-show="loading" class="animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </body>
</html>