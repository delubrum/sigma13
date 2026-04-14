<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class LoginAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('identity::auth.login');
    }

    public function asController(): Response
    {
        return $this->handle();
    }
}
