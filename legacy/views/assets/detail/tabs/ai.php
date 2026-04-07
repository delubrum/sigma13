<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<style>
    @keyframes <?= $uniqueId ?>-up { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .<?= $uniqueId ?> { animation: <?= $uniqueId ?>-up 0.4s ease-out both; font-family: 'Inter', ui-sans-serif, system-ui; }
    .sigma-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .sigma-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.5rem; display: block; }
</style>

<div class="<?= $uniqueId ?> w-full space-y-4 text-slate-900">
    
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="sigma-card">
            <span class="sigma-label">Criticidad</span>
            <span class="px-2 py-1 rounded text-[11px] font-bold bg-slate-100 ring-1 ring-slate-200" style="color: <?= $chartData['accent'] ?>; background: <?= $chartData['accent'] ?>15;">
                <?= $ai['metrics']['criticality'] ?>
            </span>
        </div>
        <div class="sigma-card">
            <span class="sigma-label">Health Score</span>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-black" style="color: <?= $chartData['accent'] ?>"><?= $ai['metrics']['health_score'] ?></span>
                <span class="text-xs font-bold text-slate-400">/100</span>
            </div>
        </div>
        <div class="sigma-card">
            <span class="sigma-label">Eventos</span>
            <span class="text-2xl font-black"><?= $ai['metrics']['total_records'] ?></span>
        </div>
        <div class="sigma-card">
            <span class="sigma-label">MTTR Global</span>
            <span class="text-2xl font-black"><?= $ai['metrics']['global_mttr'] ?><small class="text-xs font-medium ml-1">min</small></span>
        </div>
        <div class="sigma-card">
            <span class="sigma-label">Concentración</span>
            <span class="text-2xl font-black text-amber-500"><?= $ai['metrics']['pareto_percentage'] ?>%</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="sigma-card">
            <span class="sigma-label">Pareto - Frequency</span>
            <canvas id="<?= $uniqueId ?>-pareto-chart" height="180"></canvas>
        </div>
        <div class="sigma-card">
            <span class="sigma-label">MTTR by Failure Type (min)</span>
            <canvas id="<?= $uniqueId ?>-mttr-chart" height="180"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 sigma-card space-y-4 border-l-4" style="border-left-color: <?= $chartData['accent'] ?>">
            <div>
                <span class="sigma-label">Executive Summary</span>
                <p class="text-[13px] leading-relaxed text-slate-600"><?= $ai['analysis']['summary'] ?></p>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                <div>
                    <span class="sigma-label text-indigo-500">Root Cause (RCFA)</span>
                    <p class="text-[12px] font-medium"><?= $ai['analysis']['root_cause'] ?></p>
                </div>
                <div>
                    <span class="sigma-label text-red-500">Operational Risk</span>
                    <p class="text-[12px] font-medium text-red-600"><?= $ai['analysis']['risk'] ?></p>
                </div>
            </div>
        </div>

        <div class="sigma-card flex flex-col justify-between">
            <div>
                <span class="sigma-label">Action Plan</span>
                <ul class="space-y-2">
                    <?php foreach ($ai['analysis']['action_plan'] as $task) { ?>
                        <li class="text-[11px] flex items-start gap-2">
                            <span class="text-indigo-500 mt-0.5">●</span>
                            <span><?= $task ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <span class="sigma-label text-sky-600">Verdict & Recommended KPI</span>
                <p class="text-[11px] mb-2 italic">"<?= $ai['analysis']['verdict'] ?>"</p>
                <code class="text-[10px] font-mono bg-sky-50 text-sky-700 px-2 py-1 rounded block text-center border border-sky-100">
                    <?= $ai['analysis']['recommended_kpi'] ?>
                </code>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const labels = <?= $chartData['labels'] ?>;
    const accent = '<?= $chartData['accent'] ?>';

    new Chart(document.getElementById('<?= $uniqueId ?>-pareto-chart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: <?= $chartData['qty'] ?>,
                backgroundColor: labels.map((_, i) => i === 0 ? accent : '#e2e8f0'),
                borderRadius: 4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('<?= $uniqueId ?>-mttr-chart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: <?= $chartData['mttr'] ?>,
                backgroundColor: '#a855f720',
                borderColor: '#a855f7',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
    });
})();
</script>