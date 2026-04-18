<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Adapters;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class AbstractTabAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    abstract protected function view(): string;

    abstract protected function getData(int $id): mixed;

    /** Key used when passing data to the view — override if the blade expects a specific variable name */
    protected function viewKey(): string
    {
        return 'data';
    }

    public function handle(int $id): Response
    {
        return $this->hxView($this->view(), [
            $this->viewKey() => $this->getData($id),
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
