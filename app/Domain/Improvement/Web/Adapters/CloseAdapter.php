<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters;

use App\Domain\Improvement\Models\Improvement;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class CloseAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $improvement = Improvement::withCount('activities')->findOrFail($id);

        if ($improvement->activities_count === 0) {
            $this->hxNotify('Debe registrar al menos una actividad antes de cerrar.', 'error');

            return $this->hxResponse();
        }

        $this->hxModalHeader([
            'icon'     => 'ri-checkbox-circle-line',
            'title'    => 'Cerrar Mejora',
            'subtitle' => "Evaluación de cierre · #{$id}",
        ]);
        $this->hxModalWidth('40');

        return $this->hxView('improvement::modals.close', [
            'improvement' => $improvement,
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }
}
