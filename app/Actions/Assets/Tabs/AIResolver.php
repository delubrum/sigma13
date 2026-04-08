<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class AIResolver implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
            title: 'SIGMA AI Analysis',
            subtitle: '',
            icon: 'ri-robot-2-line',
            columns: [],
            formFields: []
        );
    }

    public function handle(int $id): View
    {
        $asset = Asset::findOrFail($id);

        return view('assets.tabs.ai', [
            'asset' => $asset,
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }

    public function asGenerate(Request $request, int $id): string
    {
        $asset = Asset::findOrFail($id);

        try {

            // 1. DATA MINING v3.5: FULL VISIBILITY
            $query = <<<SQL
                WITH failure_data AS (
                    SELECT 
                        m.id,
                        LOWER(TRIM(REGEXP_REPLACE(
                            COALESCE(m.description, ''), 
                            '\y(muy|buenos?|d[íi]as?|tardes?|noches?|hola|favor|revisar|revisi[oó]n|solicitud|apertur[a]|atenci[oó]n|caso|atento|saludos?|gracias|muchas|cordial(es)?|por|con|del|las?|los?|un|una|de|el|la|para)\y', 
                            '', 'gi'
                        ))) as cleaned,
                        COALESCE((SELECT SUM(duration) FROM mnt_items mi WHERE mi.mnt_id = m.id), 0) as duration
                    FROM mnt m
                    WHERE m.asset_id = :id AND m.description IS NOT NULL AND TRIM(m.description) != ''
                ),
                raw_clusters AS (
                    SELECT 
                        CASE 
                            WHEN REGEXP_REPLACE(cleaned, '[^a-z]', '', 'g') = '' THEN 'GESTION ADM / OTROS'
                            ELSE UPPER(TRIM(REGEXP_REPLACE(REGEXP_REPLACE(cleaned, '[.,()!]', '', 'g'), '\s+', ' ', 'g')))
                        END as pure_cause, 
                        COUNT(*) as qty, 
                        SUM(duration) as total_min
                    FROM failure_data
                    GROUP BY 1
                )
                SELECT 
                    pure_cause as cluster,
                    CAST(SUM(qty) AS INTEGER) as total_qty,
                    CAST(SUM(total_min) AS INTEGER) as total_minutes
                FROM raw_clusters
                GROUP BY 1
                ORDER BY total_qty DESC
                LIMIT 60 -- Suficiente para cubrir casi todo el ruido sin romper el token limit
            SQL;

            $results = DB::select($query, ['id' => $asset->id]);

            if (count($results) === 0) {
                return '<div class="p-8 text-center bg-rose-500/10 border border-border rounded-2xl font-black text-rose-600 uppercase">Sin históricos.</div>';
            }

            $metrics = ['total_events' => 0, 'total_minutes' => 0];
            $corpusString = "";
            foreach ($results as $row) {
                $metrics['total_events'] += $row->total_qty;
                $metrics['total_minutes'] += $row->total_minutes;
                $corpusString .= "- {$row->cluster}: {$row->total_qty} evts, {$row->total_minutes}m\n";
            }
            
            $globalMttr = $metrics['total_events'] > 0 ? round($metrics['total_minutes'] / $metrics['total_events'], 1) : 0;

            // 2. AI GENERATION: AGGRESSIVE UNIFICATION
            $prompt = <<<TEXT
            Eres un Ingeniero Jefe de Confiabilidad.
            ACTIVO: {$asset->brand} {$asset->model}.
            TOTAL GLOBAL: {$metrics['total_events']} eventos.

            LISTA DE HALLAZGOS (UNIFICA TODO):
            {$corpusString}

            INSTRUCCIÓN DE UNIFICACIÓN AGRESIVA:
            1. Funde los hallazgos en MAX 5 categorías técnicas reales.
            2. Ejemplo: "NIVEL ACEITE", "FUGA LUBRICANTE" y "ACEITE" deben ser una sola categoría: "SISTEMA DE LUBRICACIÓN".
            3. No dejes categorías sueltas si pueden agruparse en una mayor.
            4. La suma de todas tus categorías DEBE ser exactamente {$metrics['total_events']} eventos y {$metrics['total_minutes']} minutos.

            JSON PURO:
            {
              "health_score": int,
              "criticality": "ALTA/MEDIA/BAJA",
              "summary": "Resumen ejecutivo profesional",
              "root_cause": "Causa base estratégica",
              "risk": "Consecuencia operativa",
              "action_plan": ["Paso 1", "Paso 2", "Paso 3", "Paso 4"],
              "recommended_kpi": "...",
              "verdict": "...",
              "failures_by_frequency": [{ "term": "CATEGORÍA UNIFICADA", "qty": int, "pct": float }],
              "failures_by_impact": [{ "term": "CATEGORÍA UNIFICADA", "total_min": int, "pct": float }]
            }
            TEXT;

            $response = \Prism\Prism\Facades\Prism::text()
                ->using('groq', 'llama-3.3-70b-versatile')
                ->withSystemPrompt('Eres un procesador RCM. Tu misión es unificar variaciones en conceptos técnicos sólidos. Solo JSON.')
                ->withPrompt($prompt)
                ->generate();

            preg_match('/\{.*\}/s', $response->text ?? '', $matches);
            $jsonRaw = $matches[0] ?? '';
            /** @var array{health_score: int, criticality: string, failures_by_frequency: list<array{pct: float}>, failures_by_impact: list<array{pct: float}>} $analysis */
            $analysis = json_decode($jsonRaw, true) ?? [];

            if (!isset($analysis['failures_by_frequency']) || !isset($analysis['failures_by_impact'])) {
                throw new Exception("Error en síntesis técnica.");
            }

            $freq = $analysis['failures_by_frequency'];
            usort($freq, fn(array $a, array $b) => $b['pct'] <=> $a['pct']);
            $analysis['failures_by_frequency'] = $freq;

            $impact = $analysis['failures_by_impact'];
            usort($impact, fn(array $a, array $b) => $b['pct'] <=> $a['pct']);
            $analysis['failures_by_impact'] = $impact;

            return view('assets.tabs.ai', [
                'asset'    => $asset,
                'metrics'  => $metrics,
                'globalMttr' => $globalMttr,
                'analysis' => $analysis,
            ])->fragment('report');

        } catch (Exception $e) {
            return '<div class="p-4 bg-red-600 text-white font-black text-xs">Engine Error: ' . $e->getMessage() . '</div>';
        }
    }
}
