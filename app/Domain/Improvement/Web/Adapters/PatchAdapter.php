<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters;

use App\Domain\Improvement\Models\Improvement;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        $improvement = Improvement::findOrFail($id);
        $field = $request->string('field')->toString();
        $value = $request->input($field);

        if (! in_array($field, ['aim', 'goal', 'user_ids'], true)) {
            abort(403, 'Campo no permitido.');
        }

        if ($field === 'user_ids' && is_array($value)) {
            $value = array_map(intval(...), $value);
        }

        $improvement->update([$field => $value]);

        $this->hxNotify('Mejora actualizada correctamente');
        $this->hxRefreshTables(['dt_improvement']);

        return $this->hxResponse();
    }

    public function asController(int $id, Request $request): JsonResponse
    {
        return $this->handle($id, $request);
    }
}
