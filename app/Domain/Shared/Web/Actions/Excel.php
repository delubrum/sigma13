<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Excel
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, string $domain, string $modelName): StreamedResponse
    {
        $class = "App\\Domain\\{$domain}\\Models\\{$modelName}";
        if (! class_exists($class)) {
            $class = "App\\Domain\\{$domain}\\Models\\{$domain}";
        }

        if (! class_exists($class)) {
            App::abort(404, "Modelo {$class} no encontrado.");
        }

        $filename = "export_{$route}_".Date::today()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($class): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // Headers
            fputcsv($handle, ['ID', 'Created At'], escape: '\\');

            $class::chunk(100, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    fputcsv($handle, [$row->id, $row->created_at], escape: '\\');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function asController(Request $request, string $route): StreamedResponse
    {
        $domain = Str::studly($route);
        $modelName = Str::singular($domain);

        return $this->handle($route, $domain, $modelName);
    }
}
