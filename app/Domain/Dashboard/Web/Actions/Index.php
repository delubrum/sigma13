<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;
    use HtmxOrchestrator;

    public function asController(): Response
    {
        return $this->hxView('dashboard::index');
    }
}
