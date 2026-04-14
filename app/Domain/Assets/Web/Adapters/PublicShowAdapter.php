<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters;

use App\Domain\Assets\Actions\GetAssetPublicAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PublicShowAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $serial): Response
    {
        $payload = GetAssetPublicAction::run($serial);

        return $this->hxView('assets::public-show', $payload);
    }

    public function asController(string $serial): Response
    {
        return $this->handle($serial);
    }
}
