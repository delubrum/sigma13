<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Web\Adapters;

use App\Domain\Evaluation\Actions\ExportEvaluationsAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

final class ExportAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asController(Request $request): Response
    {
        return ExportEvaluationsAction::run(
            (string) $request->query('range', 'all'),
            (string) $request->query('start'),
            (string) $request->query('end'),
        );
    }
}
