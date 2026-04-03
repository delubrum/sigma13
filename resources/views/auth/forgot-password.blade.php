<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SIGMA') }} - Recuperar Contraseña</title>
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
                    <p class="text-neutral-500 text-sm">Recuperar contraseña</p>
                </div>

                <div class="text-center text-neutral-600 text-sm">
                    <p>Enviaremos un enlace para restablecer tu contraseña</p>
                </div>

                <form 
                    method="POST" 
                    action="{{ route('password.email') }}"
                    class="space-y-6"
                    x-data="{ loading: false }"
                    hx-post="{{ route('password.email') }}"
                    hx-swap="none"
                    @submit="loading = true"
                >
                    @csrf
                    @honeypot

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-neutral-700">Correo electrónico</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            placeholder="correo@ejemplo.com"
                            required 
                            autofocus
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-lg text-base placeholder-neutral-400 focus:outline-none focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 transition-colors @error('email') border-red-500 @enderror"
                        >
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        class="w-full py-4 bg-neutral-900 text-white font-medium text-sm tracking-wide hover:bg-neutral-800 transition-colors rounded-lg"
                        :disabled="loading"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <span>ENVIAR ENLACE</span>
                            <svg x-show="loading" class="animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-neutral-600 hover:text-neutral-900 text-sm transition-colors">
                        ← Volver al login
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>