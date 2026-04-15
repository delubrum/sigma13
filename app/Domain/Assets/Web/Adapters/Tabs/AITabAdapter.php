<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Tabs;

use App\Domain\Assets\Models\Asset;
use App\Support\HtmxOrchestrator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Prism\Prism\Facades\Prism;

final class AITabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        return $this->hxView('assets::tabs.ai', ['assetId' => $id]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }

    public function asGenerate(Request $request, Asset $asset): Response
    {

        try {
            // ── PHASE 1: Build rich text per work order ─────────────────────────
            // Prefer technician response (notes) when it has real content.
            // Fall back to request description. Concatenate both when both exist.
            // Exclude generic system notes ("Ticket set as Attended", etc).
            $query = <<<SQL
                WITH raw AS (
                    SELECT
                        m.id,
                        COALESCE(mi_agg.duration, 0) as duration,
                        COALESCE(mi_agg.complexity, '') as complexity,
                        -- Technician note preferred (most specific); fallback to request description.
                        -- Concatenate both when both carry real content.
                        TRIM(CONCAT_WS(' | ',
                            NULLIF(TRIM(COALESCE(m.description, '')), ''),
                            NULLIF(TRIM(COALESCE(mi_agg.best_note, '')), '')
                        )) as raw_text
                    FROM mnt m
                    LEFT JOIN LATERAL (
                        SELECT
                            SUM(duration) as duration,
                            -- Best note: highest specificity score = penalize generic single-word
                            -- notes and notes dominated by stopwords; favor technical density.
                            -- Score = word_count * log(length+1) — approximates IDF without a
                            -- precomputed corpus table; longer multi-word notes are more specific.
                            (SELECT notes FROM mnt_items mi2
                             WHERE mi2.mnt_id = m.id
                               AND notes IS NOT NULL
                               AND LENGTH(TRIM(notes)) > 15
                               AND notes NOT ILIKE '%ticket set%'
                               AND notes NOT ILIKE '%assignee updated%'
                               AND notes NOT ILIKE '%set as attended%'
                               AND notes NOT ILIKE '%estado actualizado%'
                             ORDER BY
                                 ARRAY_LENGTH(STRING_TO_ARRAY(TRIM(notes), ' '), 1)
                                 * LN(LENGTH(TRIM(notes)) + 1) DESC
                             LIMIT 1
                            ) as best_note,
                            MAX(complexity) FILTER (WHERE complexity IS NOT NULL AND complexity != '') as complexity
                        FROM mnt_items mi WHERE mi.mnt_id = m.id
                    ) mi_agg ON true
                    WHERE m.asset_id = :id
                      AND LENGTH(TRIM(COALESCE(m.description, ''))) >= 8
                ),
                -- ── PHASE 2: Normalize + domain-stopword stripping ───────────────
                -- Two-pass: (a) social/administrative filler, (b) high-frequency
                -- industrial generic terms that carry zero discriminative signal.
                -- These are the terms IDF would assign weight ≈ 0 in a global corpus.
                normalized AS (
                    SELECT
                        id, duration, complexity,
                        TRIM(REGEXP_REPLACE(
                            REGEXP_REPLACE(
                                REGEXP_REPLACE(
                                    REGEXP_REPLACE(
                                        LOWER(raw_text),
                                        -- (a) Social / administrative filler
                                        '\m(hola|buen[ao]s?|d[ií]as?|tardes?|noches?|favor|por favor|gracias|muchas gracias|saludos?|cordial(es)?|atento|atenta|solicito|solicitud|requiero|requiere|apertura|caso|atenci[oó]n|ayudan?|me ayudan?|urgente|asap|ticket|queda operativ[ao]|se entrega|se realizan pruebas|estimad[ao]s?|buen d[ií]a|reciba)\M',
                                        '', 'gi'
                                    ),
                                    -- (b) High-freq industrial generic terms (IDF ≈ 0 in any MNT corpus)
                                    '\m(falla|fall[oó]|fall[oó]|averí[ao]|averia|da[nñ]o|da[nñ][oó]|problema|problemas|present[ao]|presenta|present[oó]|equipo|equipos|m[aá]quina|m[aá]quinas|unidad|dispositivo|reporte|report[ao]|reporta|revis[ao]n|revisi[oó]n|revisar|revision|verificar|verificaci[oó]n|reparar|reparaci[oó]n|reparacion|realizar|realizan|correg[ií]r|correcci[oó]n|mantenimiento|servicio|atender|atiend[eo]|atiende|solicita|solicit[oó]|requiere|requiri[oó]|necesita|necesit[ao]|presento|presentar|se encuentra|se present[oó])\M',
                                    '', 'gi'
                                ),
                                '[.,;:!¡¿?()\[\]\/\\\\#@]+', '', 'g'
                            ),
                            '\s{2,}', ' ', 'g'
                        )) as norm
                    FROM raw
                    WHERE LENGTH(TRIM(raw_text)) >= 8
                ),
                -- ── PHASE 3: Exact-norm clustering (O(n), no similarity scan) ───
                -- Trigram similarity was O(n²) and duplicated work the LLM already
                -- does better semantically. GROUP BY exact norm after normalization
                -- is instant. The LLM receives up to 60 distinct patterns and
                -- merges them into failure systems — its actual strength.
                clustered AS (
                    SELECT
                        norm                                                              AS cluster_label,
                        CAST(COUNT(*) AS INTEGER)                                        AS qty,
                        CAST(SUM(duration) AS INTEGER)                                   AS total_min,
                        CAST(COUNT(*) FILTER (WHERE complexity = 'High') AS INTEGER)     AS high_complexity,
                        CAST(COUNT(*) FILTER (WHERE complexity = 'Medium') AS INTEGER)   AS med_complexity
                    FROM normalized
                    WHERE LENGTH(norm) >= 6
                    GROUP BY norm
                )
                SELECT cluster_label, qty, total_min, high_complexity, med_complexity
                FROM clustered
                ORDER BY qty DESC
                LIMIT 60
            SQL;

            $results = DB::select($query, ['id' => $asset->id]);

            if (count($results) === 0) {
                return response('<div class="p-8 text-center bg-rose-500/10 border border-border rounded-2xl font-black text-rose-600 uppercase">Sin históricos de mantenimiento.</div>');
            }

            $metrics = ['total_events' => 0, 'total_minutes' => 0, 'high_complexity' => 0, 'med_complexity' => 0];
            $corpusString = '';
            foreach ($results as $row) {
                $metrics['total_events'] += (int) $row->qty;
                $metrics['total_minutes'] += (int) $row->total_min;
                $metrics['high_complexity'] += (int) $row->high_complexity;
                $metrics['med_complexity'] += (int) $row->med_complexity;

                $complexityTag = '';
                if ((int) $row->high_complexity > 0) {
                    $complexityTag = ' [HIGH:'.$row->high_complexity.']';
                } elseif ((int) $row->med_complexity > 0) {
                    $complexityTag = ' [MED:'.$row->med_complexity.']';
                }

                $corpusString .= "- ({$row->qty}x, {$row->total_min}min{$complexityTag}) {$row->cluster_label}\n";
            }

            $globalMttr = $metrics['total_events'] > 0
                ? round($metrics['total_minutes'] / $metrics['total_events'], 1)
                : 0;

            $lowComplexity = $metrics['total_events'] - $metrics['high_complexity'] - $metrics['med_complexity'];
            $complexityContext = "Complejidad: {$metrics['high_complexity']} alta, {$metrics['med_complexity']} media, {$lowComplexity} baja. MTTR global: {$globalMttr} min.";

            $prompt = <<<TEXT
            Activo: {$asset->brand} {$asset->model} (tipo: {$asset->kind}).
            Histórico deduplicado: {$metrics['total_events']} órdenes · {$metrics['total_minutes']} min totales · {$complexityContext}

            CLUSTERS (ocurrencias, minutos, complejidad [HIGH/MED = intervención técnica compleja]):
            {$corpusString}

            INSTRUCCIONES:
            - Agrupa clusters en 3-5 SISTEMAS DE FALLA técnicos específicos al tipo de activo.
            - Categorías válidas (ejemplos — adapta al equipo): LUBRICACIÓN, SISTEMA ELÉCTRICO, SISTEMA MECÁNICO, HIDRÁULICO, NEUMÁTICO, ESTRUCTURA, INSTRUMENTACIÓN, CONSUMIBLES, SOFTWARE/FIRMWARE, CONECTIVIDAD, REFRIGERACIÓN, TRANSMISIÓN, FRENOS.
            - PROHIBIDO: OTROS, OTHER, VARIOS, MISCELÁNEOS, SIN CLASIFICAR, DESCONOCIDO, ADMINISTRATIVO, GESTIÓN, o cualquier variante genérica. Cada cluster debe ir en una categoría técnica real.
            - health_score 0-100: penaliza por frecuencia alta, MTTR alto y proporción de intervenciones HIGH/MED. 100 = sin fallas.
            - qty y total_min en failures_* deben sumar exactamente {$metrics['total_events']} y {$metrics['total_minutes']}.

            Responde SOLO JSON válido:
            {
              "health_score": int,
              "criticality": "ALTA|MEDIA|BAJA",
              "summary": "2-3 oraciones ejecutivas sobre el patrón de fallas dominante",
              "root_cause": "causa raíz técnica principal en una frase",
              "risk": "consecuencia operativa concreta si no se interviene",
              "verdict": "hallazgo clave en máximo 15 palabras",
              "action_plan": ["acción correctiva 1", "acción correctiva 2", "acción preventiva 3", "acción predictiva 4"],
              "failures_by_frequency": [{"term": "CATEGORÍA", "qty": int, "pct": float}],
              "failures_by_impact": [{"term": "CATEGORÍA", "total_min": int, "pct": float}]
            }
            TEXT;

            $response = Prism::text()
                ->using('groq', 'llama-3.3-70b-versatile')
                ->withSystemPrompt('Eres un procesador RCM. Tu misión es unificar variaciones en conceptos técnicos sólidos. Solo JSON.')
                ->withPrompt($prompt)
                ->generate();

            preg_match('/\{.*\}/s', $response->text ?? '', $matches);
            $jsonRaw = $matches[0] ?? '';

            /** @var array{health_score: int, criticality: string, failures_by_frequency: list<array{pct: float}>, failures_by_impact: list<array{pct: float}>} $analysis */
            $analysis = json_decode($jsonRaw, true) ?? [];

            if (! isset($analysis['failures_by_frequency']) || ! isset($analysis['failures_by_impact'])) {
                throw new Exception('Error en síntesis técnica.');
            }

            // Merge any catch-all categories into the top real technical category
            $catchAll = ['OTROS', 'OTHER', 'GESTIÓN', 'ADMINISTRATIVO', 'MISCELÁNEOS', 'SIN CLASIFICAR', 'DESCONOCIDO'];
            $isCatchAll = fn (string $term): bool => in_array(strtoupper(trim($term)), $catchAll, true)
                || str_contains(strtoupper($term), 'OTRO')
                || str_contains(strtoupper($term), 'OTHER');

            foreach (['failures_by_frequency' => 'qty', 'failures_by_impact' => 'total_min'] as $key => $field) {
                $real = array_filter($analysis[$key], fn (array $f): bool => ! $isCatchAll($f['term']));
                $garbage = array_filter($analysis[$key], fn (array $f): bool => $isCatchAll($f['term']));
                if ($garbage && $real) {
                    $extra = array_sum(array_column(array_values($garbage), $field));
                    usort($real, fn (array $a, array $b): int => $b[$field] <=> $a[$field]);
                    $real = array_values($real);
                    $real[0][$field] += $extra;
                    $analysis[$key] = $real;
                }
            }

            // Recompute pct from actual clustered totals — never trust LLM math
            usort($analysis['failures_by_frequency'], fn (array $a, array $b): int => $b['qty'] <=> $a['qty']);
            usort($analysis['failures_by_impact'], fn (array $a, array $b): int => $b['total_min'] <=> $a['total_min']);

            $totalQty = array_sum(array_column($analysis['failures_by_frequency'], 'qty'));
            $totalMin = array_sum(array_column($analysis['failures_by_impact'], 'total_min'));

            foreach ($analysis['failures_by_frequency'] as &$f) {
                $f['pct'] = $totalQty > 0 ? round($f['qty'] / $totalQty * 100, 1) : 0;
            }
            foreach ($analysis['failures_by_impact'] as &$f) {
                $f['pct'] = $totalMin > 0 ? round($f['total_min'] / $totalMin * 100, 1) : 0;
            }
            unset($f);

            // Update metrics to reflect what LLM actually clustered
            $metrics['total_events'] = $totalQty;
            $metrics['total_minutes'] = $totalMin;
            $globalMttr = $totalQty > 0 ? round($totalMin / $totalQty, 1) : 0;

            return response(
                view('assets::tabs.ai', [
                    'asset' => $asset,
                    'assetId' => $asset->id,
                    'metrics' => $metrics,
                    'globalMttr' => $globalMttr,
                    'analysis' => $analysis,
                ])->fragment('report')
            );

        } catch (Exception $e) {
            return response('<div class="p-4 bg-red-600 text-white font-black text-xs">Engine Error: '.e($e->getMessage()).'</div>');
        }
    }
}
