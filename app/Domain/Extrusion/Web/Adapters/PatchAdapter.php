<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Web\Adapters;

use App\Domain\Extrusion\Models\Die;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        /** @var Die $die */
        $die   = Die::query()->findOrFail($id);
        $field = (string) $request->input('field');

        $jsonFields = ['clicks', 'systema'];

        if (in_array($field, $jsonFields, true)) {
            $value = $request->input($field, []);
            $die->$field = is_array($value) ? $value : (array) $value;
        } else {
            $allowed = ['company_id', 'category_id', 'b', 'h', 'e1', 'e2'];
            if (! in_array($field, $allowed, true)) {
                return $this->hxNotify('error', 'Campo no permitido.')->hxResponse();
            }
            $die->$field = $request->input($field);
        }

        $die->save();

        return $this->hxNotify('success', 'Actualizado.')
            ->hxRefreshTables(['dt_extrusion'])
            ->hxResponse();
    }

    public function asController(int $id, Request $request): JsonResponse
    {
        return $this->handle($id, $request);
    }

    public function uploadFile(int $id, Request $request): JsonResponse
    {
        /** @var Die $die */
        $die  = Die::query()->findOrFail($id);
        $file = $request->file('file');

        if ($file === null) {
            return $this->hxNotify('error', 'No file received.')->hxResponse();
        }

        $shape = $die->geometry_shape;
        $name  = $file->getClientOriginalName();
        $path  = "matrices/{$shape}/{$name}";

        Storage::disk('public')->put($path, $file->getContent());

        return $this->hxNotify('success', 'Uploaded.')
            ->hxRefreshTables(['dt_extrusion'])
            ->hxResponse();
    }

    public function deleteFile(int $id, Request $request): JsonResponse
    {
        /** @var Die $die */
        $die      = Die::query()->findOrFail($id);
        $filename = basename((string) $request->input('filename', ''));
        $path     = "matrices/{$die->geometry_shape}/{$filename}";

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return $this->hxNotify('success', 'Deleted.')
            ->hxRefreshTables(['dt_extrusion'])
            ->hxResponse();
    }
}
