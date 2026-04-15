<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Prism\Prism\Facades\Prism;

final class AiSearchAction
{
    use AsAction;

    /** @return array<string,mixed> */
    public function handle(string $query): array
    {
        $companies  = DB::table('matrices')->distinct()
            ->whereNotNull('company_id')->where('company_id', '!=', '')
            ->pluck('company_id')->implode(', ');

        $categories = DB::table('matrices_db')->where('kind', 'Category')
            ->orderBy('name')->pluck('name')->implode(', ');

        $prompt = <<<TEXT
        Extrae parámetros para buscar matrices de extrusión de aluminio.
        Empresas reales: {$companies}
        Categorías reales: {$categories}

        MAPEO ESTRICTO de operadores:
        - "mayor a X", "más de X", "> X" → campo_gt
        - "menor a X", "menos de X", "< X" → campo_lt
        - "entre X y Y", "de X a Y", "X - Y" → campo_min + campo_max
        - "alrededor de X", "aprox X", "~X" → campo_min: X-0.5, campo_max: X+0.5
        - "X" sin calificador → campo + tolerance:0.05

        Dimensiones válidas: b, h, e1, e2
        Otros campos: company (empresa), category (categoría)

        Responde SOLO JSON válido sin markdown.
        Texto: "{$query}"
        TEXT;

        $response = Prism::text()
            ->using('groq', 'llama-3.1-8b-instant')
            ->withSystemPrompt('Eres un parser de búsquedas técnicas. Solo responde JSON.')
            ->withPrompt($prompt)
            ->generate();

        $raw = trim((string) ($response->text ?? ''));
        $raw = (string) preg_replace('/```json|```/', '', $raw);

        /** @var array<string,mixed> $decoded */
        $decoded = json_decode($raw, true) ?? [];

        return $decoded;
    }
}
