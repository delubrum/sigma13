<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters\Modals;

use App\Domain\Improvement\Models\Improvement;
use App\Domain\Improvement\Models\ImprovementCause;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class CauseModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asCreate(int $id): Response
    {
        $this->hxModalHeader([
            'icon' => 'ri-search-eye-line',
            'title' => 'Registrar Causa',
            'subtitle' => "Mejora #{$id}",
        ]);
        $this->hxModalWidth('50');

        return $this->hxView('improvement::modals.cause', [
            'improvementId' => $id,
            'cause' => null,
        ]);
    }

    public function asShow(int $id, int $causeId): Response
    {
        $cause = ImprovementCause::findOrFail($causeId);

        $this->hxModalHeader([
            'icon' => 'ri-search-eye-line',
            'title' => 'Causa Registrada',
            'subtitle' => "Mejora #{$id}",
        ]);
        $this->hxModalWidth('50');

        return $this->hxView('improvement::modals.cause', [
            'improvementId' => $id,
            'cause' => $cause,
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $improvementId = $request->integer('improvement_id');
        $method = $request->integer('method');

        $request->validate([
            'improvement_id' => ['required', 'integer'],
            'reason' => ['required', 'string'],
            'method' => ['required', 'in:1,2'],
            'probable' => ['required', 'string'],
        ]);

        $filePath = null;
        if ($method === 2 && $request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = Storage::disk('public')->putFileAs(
                "improvement/other/{$improvementId}",
                $file,
                time().'_'.$file->getClientOriginalName()
            );
        }

        $whys = null;
        if ($method === 1) {
            $whys = array_values(array_filter($request->input('whys', []), static fn ($v) => ! blank($v)));
        }

        $id = $request->integer('id') ?: null;

        if ($id) {
            ImprovementCause::findOrFail($id)->update([
                'reason' => $request->string('reason')->toString(),
                'method' => $method,
                'probable' => $request->string('probable')->toString(),
                'whys' => $whys ? json_encode($whys) : null,
                'file' => $filePath,
            ]);
        } else {
            ImprovementCause::create([
                'improvement_id' => $improvementId,
                'reason' => $request->string('reason')->toString(),
                'method' => $method,
                'probable' => $request->string('probable')->toString(),
                'whys' => $whys ? json_encode($whys) : null,
                'file' => $filePath,
            ]);

            Improvement::findOrFail($improvementId)->update(['status' => 'Plan']);
        }

        $this->hxNotify('Causa registrada.');
        $this->hxRefresh(['#tab-content']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }

    public function destroy(int $causeId): JsonResponse
    {
        ImprovementCause::findOrFail($causeId)->delete();

        $this->hxNotify('Causa eliminada.');
        $this->hxRefresh(['#tab-content']);

        return $this->hxResponse();
    }

    public function asData(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', 15);

        $paginator = ImprovementCause::query()
            ->where('improvement_id', $id)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        $items = $paginator->map(function (ImprovementCause $c): array {
            $method = match ($c->method) {
                1 => '5 Whys',
                2 => 'Archivo',
                default => '-',
            };

            $actions = '<button class="btn-icon" hx-get="'.route('improvement.causes.show', [$c->improvement_id, $c->id]).'" hx-target="#modal-body-2" hx-swap="innerHTML"><i class="ri-eye-line"></i></button> ';
            $actions .= '<button class="btn-icon text-red-500" hx-delete="'.route('improvement.causes.delete', $c->id).'" hx-confirm="¿Eliminar esta causa?"><i class="ri-delete-bin-line"></i></button>';

            return [
                'id' => $c->id,
                'reason' => $c->reason,
                'method' => $method,
                'probable' => $c->probable,
                'actions' => $actions,
            ];
        })->values()->all();

        return response()->json([
            'data' => $items,
            'last_page' => $paginator->lastPage(),
            'last_row' => $paginator->total(),
        ]);
    }
}
