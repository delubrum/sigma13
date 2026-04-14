<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DashboardAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('dashboard::index');
    }

    public function asController(): Response
    {
        return $this->handle();
    }
}
