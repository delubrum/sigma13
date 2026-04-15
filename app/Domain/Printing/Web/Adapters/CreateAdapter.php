<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        $index = new IndexAdapter;
        $config = $index->config();

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => 'New Work Order',
            'subtitle' => $config->subtitle ?? '',
        ]);
        $this->hxModalWidth($config->modalWidth);

        return $this->hxView('components::new-modal', [
            'route' => 'printing',
            'config' => $config,
            'data' => [],
            'customPostRoute' => route('printing.save'),
        ]);
    }

    public function asController(): Response
    {
        return $this->handle();
    }
}
