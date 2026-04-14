<div class="mb-5 relative" id="tab-ai-container">
    <div class="flex items-center justify-between mb-3 border-b pb-3" style="border-color:var(--b)">
        <h2 class="text-base font-bold flex items-center gap-1.5" style="color:var(--tx)">
            <i class="ri-robot-2-line text-xl opacity-70"></i>
            <span>SIGMA AI - Análisis de Confiabilidad</span>
        </h2>

        <button
            class="px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider flex items-center space-x-1.5 transition-all outline-none active:scale-95"
            style="background:var(--ac); color:var(--ac-inv)"
            hx-post="{{ route('assets.ai.generate', $assetId) }}"
            hx-target="#ai-results"
            hx-indicator="#ai-loading">
            <i class="ri-magic-line text-sm"></i>
            <span>Generar Análisis de Fallas</span>
        </button>
    </div>

    <!-- AI Results Container -->
    <div class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden min-h-[500px] relative mt-4 shadow-inner">
        
        <!-- Loading State -->
        <div id="ai-loading" class="htmx-indicator absolute inset-0 bg-white/70 dark:bg-slate-900/90 backdrop-blur-md flex flex-col items-center justify-center z-10">
            <div class="relative w-16 h-16 mb-6">
                <div class="absolute inset-0 rounded-full border-4 border-slate-200 dark:border-white/5"></div>
                <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
                <i class="ri-robot-2-line absolute inset-0 flex items-center justify-center text-xl text-emerald-500"></i>
            </div>
            <p class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white animate-pulse">Analizando Históricos Industriales</p>
        </div>

        <!-- Result Content -->
        <div id="ai-results" class="w-full h-full p-6">
            @fragment('report')
                @if(isset($analysis) && isset($analysis['failures_by_frequency']))
                    @php
                        $hs = $analysis['health_score'] ?? 0;
                        $color = match(true) {
                            $hs >= 80 => 'emerald',
                            $hs >= 50 => 'amber',
                            default   => 'rose'
                        };
                        $criticality = $analysis['criticality'] ?? 'BAJA';
                        $critClass = match($criticality) {
                            'ALTA' => 'bg-rose-600 text-white shadow-lg',
                            'MEDIA' => 'bg-amber-600 text-white shadow-lg',
                            default => 'bg-emerald-600 text-white shadow-lg'
                        };
                    @endphp

                    <div class="animate-in fade-in slide-in-from-bottom-6 duration-1000">
                        
                        <!-- Top Header: Health & Stats -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
                            
                            <!-- Health Gauge Card -->
                            <div class="lg:col-span-4 relative group h-full">
                                <div class="relative flex flex-col items-center justify-center p-10 rounded-3xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-800 shadow-xl h-full">
                                    <div class="relative w-44 h-44 mb-6">
                                        <svg class="w-full h-full transform -rotate-90">
                                            <circle cx="88" cy="88" r="76" stroke="currentColor" stroke-width="14" fill="transparent" class="text-slate-200 dark:text-white/5" />
                                            <circle cx="88" cy="88" r="76" stroke="currentColor" stroke-width="14" fill="transparent" 
                                                stroke-dasharray="477.5" 
                                                stroke-dashoffset="{{ 477.5 * (1 - $hs / 100) }}" 
                                                class="text-{{ $color }}-600 transition-all duration-1000 ease-out" 
                                                stroke-linecap="round" />
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-6xl font-black tracking-tighter text-slate-950 dark:text-white">{{ $hs }}%</span>
                                            <span class="text-xs uppercase tracking-widest font-black text-slate-600 dark:text-slate-400">HEALTH</span>
                                        </div>
                                    </div>
                                    <div class="inline-flex items-center px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest {{ $critClass }}">
                                        CRITICIDAD {{ $criticality }}
                                    </div>
                                </div>
                            </div>

                            <!-- Executive Summary Card -->
                            <div class="lg:col-span-8 flex flex-col gap-6 h-full">
                                <div class="p-10 rounded-3xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-800 shadow-xl h-full relative overflow-hidden">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-600/10 flex items-center justify-center text-emerald-600">
                                            <i class="ri-article-line text-2xl"></i>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-950 dark:text-white uppercase tracking-tight">Análisis Estratégico</h3>
                                    </div>
                                    <p class="text-slate-800 dark:text-slate-300 text-base leading-relaxed font-bold">
                                        {{ $analysis['summary'] }}
                                    </p>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-10 pt-10 border-t border-slate-200 dark:border-white/5">
                                        <div class="space-y-1">
                                            <div class="text-[11px] font-black text-slate-600 dark:text-slate-500 uppercase tracking-widest">Eventos Totales</div>
                                            <div class="text-2xl font-black text-slate-950 dark:text-white">{{ $metrics['total_events'] }}</div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-[11px] font-black text-slate-600 dark:text-slate-500 uppercase tracking-widest">MTTR Global</div>
                                            <div class="text-2xl font-black text-slate-950 dark:text-white">{{ $globalMttr }}<span class="text-xs ml-1">m</span></div>
                                        </div>
                                        <div class="space-y-1 col-span-2">
                                            <div class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest flex items-center gap-1">
                                                <i class="ri-flashlight-line"></i> Hallazgo Maestro
                                            </div>
                                            <div class="text-sm font-black text-slate-950 dark:text-slate-200 leading-tight">{{ $analysis['verdict'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pareto Section (Dual Graphs) -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            
                            <!-- Pareto de Frecuencia -->
                            <div class="p-10 rounded-3xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-800 shadow-xl">
                                <div class="flex items-center justify-between mb-10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg">
                                            <i class="ri-list-check text-xl"></i>
                                        </div>
                                        <h4 class="text-sm font-black uppercase tracking-widest text-slate-950 dark:text-white">Frecuencia de Falla</h4>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Ocurrencias</span>
                                </div>
                                <div class="space-y-10">
                                    @foreach($analysis['failures_by_frequency'] as $f)
                                        <div class="space-y-3">
                                            <div class="flex justify-between text-xs font-black uppercase tracking-tight">
                                                <span class="text-slate-950 dark:text-slate-300">{{ $f['term'] }}</span>
                                                <span class="text-slate-900 dark:text-slate-400">{{ $f['qty'] }} evts ({{ $f['pct'] }}%)</span>
                                            </div>
                                            <div class="h-4 w-full bg-slate-200 dark:bg-white/5 rounded-full overflow-hidden border border-slate-300 dark:border-transparent">
                                                <div class="h-full bg-linear-to-r from-blue-600 to-indigo-600 rounded-full transition-all duration-1000 shadow-lg" style="width: {{ $f['pct'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Pareto de Impacto -->
                            <div class="p-10 rounded-3xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-800 shadow-xl">
                                <div class="flex items-center justify-between mb-10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-rose-600 flex items-center justify-center text-white shadow-lg">
                                            <i class="ri-time-line text-xl"></i>
                                        </div>
                                        <h4 class="text-sm font-black uppercase tracking-widest text-slate-950 dark:text-white">Impacto Operativo</h4>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Minutos Totales</span>
                                </div>
                                <div class="space-y-10">
                                    @foreach($analysis['failures_by_impact'] as $f)
                                        <div class="space-y-3">
                                            <div class="flex justify-between text-xs font-black uppercase tracking-tight">
                                                <span class="text-slate-950 dark:text-slate-300">{{ $f['term'] }}</span>
                                                <span class="text-slate-900 dark:text-slate-400">{{ $f['total_min'] }}m ({{ $f['pct'] }}%)</span>
                                            </div>
                                            <div class="h-4 w-full bg-slate-200 dark:bg-white/5 rounded-full overflow-hidden border border-slate-300 dark:border-transparent">
                                                <div class="h-full bg-linear-to-r from-rose-600 to-amber-600 rounded-full transition-all duration-1000 shadow-lg" style="width: {{ $f['pct'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Final Plan -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <div class="lg:col-span-8 p-10 rounded-3xl border border-slate-300 dark:border-white/10 bg-white dark:bg-slate-800 shadow-2xl">
                                <div class="flex items-center gap-4 mb-10">
                                    <div class="w-12 h-12 rounded-full bg-emerald-600 flex items-center justify-center text-white shadow-xl">
                                        <i class="ri-shield-check-line text-2xl"></i>
                                    </div>
                                    <hspan class="text-sm font-black uppercase tracking-widest text-slate-950 dark:text-white">Plan Acción Estratégico RCM</hspan>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                    @foreach($analysis['action_plan'] as $idx => $step)
                                        <div class="flex items-start gap-4">
                                            <span class="text-3xl font-black text-slate-300 dark:text-white/10">0{{ $idx + 1 }}</span>
                                            <p class="text-[14px] font-black text-slate-900 dark:text-slate-400 leading-relaxed">{{ $step }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="lg:col-span-4 flex flex-col gap-6">
                                <div class="p-10 rounded-3xl bg-blue-600 text-white shadow-xl shadow-blue-600/20">
                                    <div class="text-[11px] font-black uppercase tracking-widest opacity-80 mb-3 text-white">Causa Raíz</div>
                                    <p class="text-lg font-black leading-tight">{{ $analysis['root_cause'] }}</p>
                                </div>
                                <div class="p-10 rounded-3xl bg-rose-600 text-white shadow-xl shadow-rose-600/20">
                                    <div class="text-[11px] font-black uppercase tracking-widest opacity-80 mb-3 text-white">Riesgo Real</div>
                                    <p class="text-lg font-black leading-tight">{{ $analysis['risk'] }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="py-32 flex flex-col items-center justify-center text-center">
                        <i class="ri-robot-2-line text-8xl text-slate-200 dark:text-slate-700 mb-6 group-hover:-translate-y-4 transition-transform duration-500"></i>
                        <h3 class="text-xl font-black uppercase tracking-widest text-slate-950 dark:text-white">Sigma AI Ready</h3>
                        <p class="text-sm mt-4 font-bold text-slate-600 dark:text-slate-500 max-w-sm uppercase tracking-tighter">Motor de análisis RCM con clústeres Postgres nativos y normalización LLM.</p>
                    </div>
                @endif
            @endfragment
        </div>
    </div>
</div>
