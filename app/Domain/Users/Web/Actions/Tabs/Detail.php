<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Actions\Tabs;

use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Detail
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $user = User::findOrFail($id);
        
        return $this->hxView('users::tabs.detail', [
            'user' => $user,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
