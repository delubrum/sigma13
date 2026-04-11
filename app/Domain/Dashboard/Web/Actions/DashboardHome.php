<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Web\Actions;

use App\Domain\Dashboard\Actions\LoadSidebar;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

final class DashboardHome
{
    use AsAction;

    public function asController(): View
    {
        return view('home');
    }
}
