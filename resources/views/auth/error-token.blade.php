<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIGMA - {{ $title }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-neutral-900 font-sans antialiased">
        <div class="min-h-screen flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm text-center">

                <div class="text-center mb-10">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SIGMA" class="h-14 w-auto mx-auto">
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400 mt-4">
                        Error de Autenticación
                    </p>
                </div>

                <h2 class="text-xl font-medium mb-6">{{ $message }}</h2>
                
                <p class="text-sm text-neutral-500 mb-10 leading-relaxed">
                    Por seguridad, los enlaces de recuperación expiran tras un periodo de tiempo. Por favor, solicita uno nuevo desde la pantalla de acceso.
                </p>

                <a href="{{ route('login') }}" 
                   class="inline-block w-full py-5 bg-neutral-900 text-white font-bold text-[11px] tracking-[0.3em] hover:bg-neutral-800 transition-all shadow-sm active:scale-[0.98]">
                    VOLVER AL LOGIN
                </a>

            </div>
        </div>
    </body>
</html>