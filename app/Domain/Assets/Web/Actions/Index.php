<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Actions\Index as GetConfig;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'assets',
            'config' => GetConfig::run(),
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        return GetConfig::make()->asData($request);
    }
}
