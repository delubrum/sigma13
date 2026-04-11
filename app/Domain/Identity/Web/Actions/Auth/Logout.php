<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Actions\Auth;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class Logout
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
