<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Actions;

use Illuminate\Contracts\View\View;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;

    public function asController(): View
    {
        return view('home');
    }
}
