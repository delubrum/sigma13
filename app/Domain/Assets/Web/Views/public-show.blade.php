<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMA | {{ $asset->serial }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.6s ease-out forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen pb-12 overflow-x-hidden">

    {{-- Contact Floating Button --}}
    <a href="mailto:soporte@tuempresa.com" target="_blank" rel="noopener noreferrer" 
       class="fixed bottom-6 right-6 z-[100] bg-indigo-600 text-white p-4 rounded-full shadow-2xl hover:bg-indigo-700 transition-all hover:scale-110 active:scale-95 group">
        <i class="ri-customer-service-2-fill text-2xl"></i>
        <span class="absolute right-full mr-3 bg-slate-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap hidden sm:block">SOPORTE TÉCNICO</span>
    </a>

    {{-- Main Container --}}
    <div class="max-w-4xl mx-auto px-4 pt-8">
        
        {{-- Header Card --}}
        <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100 mb-6 animate-fade-up">
            <div class="relative h-64 sm:h-80 bg-slate-900">
                @if($asset->profile_photo_url)
                    <img src="{{ $asset->profile_photo_url }}" class="w-full h-full object-contain animate-fade-in transition-all">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-800">
                        <i class="ri-image-line text-6xl opacity-20"></i>
                        <span class="text-xs uppercase tracking-widest mt-4 font-bold opacity-40">Sin Imagen del Activo</span>
                    </div>
                @endif
                
                {{-- Status Badge --}}
                <div class="absolute top-6 right-6">
                    <span class="px-4 py-2 rounded-full text-xs font-extrabold uppercase tracking-tighter shadow-xl border backdrop-blur-md 
                        @switch($asset->status)
                            @case('available') bg-emerald-500/20 text-emerald-100 border-emerald-400/50 @break
                            @case('assigned') bg-blue-500/20 text-blue-100 border-blue-400/50 @break
                            @case('maintenance') bg-amber-500/20 text-amber-100 border-amber-400/50 @break
                            @default bg-slate-500/20 text-slate-100 border-slate-400/50
                        @endswitch">
                        {{ $asset->status }}
                    </span>
                </div>

            </div>
            
            {{-- Info Section (Below Photo) --}}
            <div class="bg-white p-6 sm:p-8 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-slate-900 text-3xl font-extrabold tracking-tight">{{ $asset->brand }} {{ $asset->model }}</h1>
                        <p class="text-slate-500 text-sm font-medium mt-1 flex items-center gap-2 uppercase tracking-widest">
                            <i class="ri-hashtag text-indigo-500"></i> {{ $asset->serial }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                         <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Estado Actual</span>
                         <span class="w-2 h-2 rounded-full @if($asset->status === 'available') bg-emerald-500 @elseif($asset->status === 'assigned') bg-blue-500 @else bg-amber-500 @endif animate-pulse"></span>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 border-t border-slate-100">
                <div class="p-4 border-r border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Hostname</span>
                    <span class="text-xs font-bold text-slate-700 break-all">{{ $asset->hostname ?: 'N/A' }}</span>
                </div>
                <div class="p-4 border-r border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">SAP ID</span>
                    <span class="text-xs font-bold text-slate-700">{{ $asset->sap ?: 'N/A' }}</span>
                </div>
                <div class="p-4 border-r border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Ubicación</span>
                    <span class="text-xs font-bold text-slate-700">{{ $asset->location ?: 'N/A' }}</span>
                </div>
                <div class="p-4 border-r border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Modo</span>
                    <span class="text-xs font-bold text-slate-700">{{ $asset->work_mode ?: 'N/A' }}</span>
                </div>
                <div class="p-4 border-r border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Teléfono</span>
                    <span class="text-xs font-bold text-slate-700">{{ $asset->phone ?: 'N/A' }}</span>
                </div>
                <div class="p-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Responsable</span>
                    <span class="text-xs font-bold text-indigo-600 truncate block">{{ $asset->currentAssignment->employee->name ?? 'Disponible' }}</span>
                </div>
            </div>
        </div>

        {{-- Technical Specs Card --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 opacity-0 animate-fade-up delay-1" style="animation-fill-mode: forwards;">
            <div class="md:col-span-2 bg-white rounded-[1.5rem] p-8 shadow-lg shadow-slate-200/50 border border-slate-100 transition-transform hover:scale-[1.01]">
                <h2 class="text-lg font-extrabold text-slate-800 mb-6 flex items-center gap-3">
                    <i class="ri-settings-5-line text-indigo-600 p-2 bg-indigo-50 rounded-xl"></i>
                    Ficha Técnica
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="ri-cpu-line text-slate-400"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Procesador</span>
                            <span class="text-sm font-semibold text-slate-700">{{ $asset->cpu ?: 'No especificado' }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="ri-ram-line text-slate-400"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Memoria RAM</span>
                            <span class="text-sm font-semibold text-slate-700">{{ $asset->ram ?: 'No especificado' }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="ri-hard-drive-2-line text-slate-400"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Almacenamiento</span>
                            <span class="text-sm font-semibold text-slate-700">SSD: {{ $asset->ssd ?: 'N/A' }} / HDD: {{ $asset->hdd ?: 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center shrink-0">
                            <i class="ri-windows-fill text-slate-400"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">S.O.</span>
                            <span class="text-sm font-semibold text-slate-700">{{ $asset->so ?: 'No especificado' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity Card --}}
            <div class="bg-indigo-700 rounded-[1.5rem] p-8 text-white shadow-xl shadow-indigo-200/50 flex flex-col justify-center relative overflow-hidden">
                <i class="ri-shield-check-line absolute -right-4 -bottom-4 text-9xl opacity-10"></i>
                <h2 class="text-lg font-extrabold mb-2">Estado Local</h2>
                <p class="text-indigo-200 text-sm mb-6 leading-relaxed">Este activo se encuentra verificado por el departamento de IT de SIGMA.</p>
                <div class="space-y-4">
                    <div class="bg-indigo-600/50 rounded-xl p-4 border border-indigo-400/20">
                        <span class="block text-[10px] font-bold uppercase opacity-60">Última Revisión</span>
                        <span class="text-sm font-bold">14 Oct, 2026</span>
                    </div>
                    <div class="bg-indigo-600/50 rounded-xl p-4 border border-indigo-400/20">
                        <span class="block text-[10px] font-bold uppercase opacity-60">Próximo Preventivo</span>
                        <span class="text-sm font-bold text-amber-300">Marzo, 2027</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maintenances Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-0 animate-fade-up delay-2" style="animation-fill-mode: forwards;">
            {{-- Correctives --}}
            <div class="bg-white rounded-[1.5rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="ri-tools-line text-indigo-600 font-bold"></i>
                    <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Últimos Correctivos</h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-slate-50">
                            @forelse($correctives as $mnt)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($mnt->ended_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 line-clamp-2" title="{{ $mnt->description }}">
                                        {{ $mnt->description }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">No hay registros correctivos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Preventives --}}
            <div class="bg-white rounded-[1.5rem] shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="ri-calendar-check-line text-emerald-600 font-bold"></i>
                    <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Últimos Preventivos</h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-slate-50">
                            @forelse($preventives as $mnt)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($mnt->last_performed_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 line-clamp-2" title="{{ $mnt->activity }}">
                                        {{ $mnt->activity }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">No hay registros preventivos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-12 text-center">
            <img src="https://sigma13.app/images/logo.png" style="filter: grayscale(1); opacity: 0.2;" class="h-6 mx-auto mb-4 grayscale">
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em]">&copy; 2026 SIGMA Orchestrator System</p>
        </div>

    </div>

</body>
</html>
