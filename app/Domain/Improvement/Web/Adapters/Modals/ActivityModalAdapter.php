<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters\Modals;

use App\Domain\Improvement\Models\ImprovementActivity;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class ActivityModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): void {}

    public function asCreate(int $id): Response
    {
        $users = DB::table('users')->where('is_active', true)->orderBy('username')->get(['id', 'username as name']);

        $this->hxModalHeader([
            'icon' => 'ri-task-line',
            'title' => 'Nueva Actividad',
            'subtitle' => "Mejora #{$id}",
        ]);
        $this->hxModalWidth('50');

        return $this->hxView('improvement::modals.activity', [
            'improvementId' => $id,
            'activity' => null,
            'users' => $users,
        ]);
    }

    public function asEdit(int $id, int $activityId): Response
    {
        $activity = ImprovementActivity::findOrFail($activityId);
        $users = DB::table('users')->where('is_active', true)->orderBy('username')->get(['id', 'username as name']);

        $this->hxModalHeader([
            'icon' => 'ri-task-line',
            'title' => 'Editar Actividad',
            'subtitle' => "Mejora #{$id}",
        ]);
        $this->hxModalWidth('50');

        return $this->hxView('improvement::modals.activity', [
            'improvementId' => $id,
            'activity' => $activity,
            'users' => $users,
        ]);
    }

    public function asClose(int $id, int $activityId): Response
    {
        $activity = ImprovementActivity::findOrFail($activityId);

        $this->hxModalHeader([
            'icon' => 'ri-clipboard-check-line',
            'title' => 'Registrar Resultado',
            'subtitle' => "Actividad #{$activityId}",
        ]);
        $this->hxModalWidth('45');

        return $this->hxView('improvement::modals.activity-close', [
            'improvementId' => $id,
            'activity' => $activity,
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $id = $request->integer('id') ?: null;
        $improvementId = $request->integer('improvement_id');
        $isClose = $request->boolean('is_close');

        if ($isClose) {
            $request->validate([
                'activity_id' => ['required', 'integer'],
                'done_date' => ['required', 'date'],
                'results' => ['required', 'string'],
                'fulfill' => ['required', 'boolean'],
            ]);

            $activityId = $request->integer('activity_id');
            $activity = ImprovementActivity::findOrFail($activityId);

            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = Storage::disk('public')->putFileAs(
                    "improvement/result/{$improvementId}",
                    $file,
                    time().'_'.$file->getClientOriginalName()
                );
            }

            $existing = is_array($activity->results) ? $activity->results : [];
            $existing[] = [
                $request->string('done_date')->toString(),
                $request->string('results')->toString(),
                $filePath ?? '',
            ];

            $fulfill = $request->boolean('fulfill');

            $activity->update([
                'results' => json_encode($existing),
                'done' => $fulfill ? now() : null,
                'fulfill' => $fulfill,
            ]);

            $this->hxNotify('Resultado registrado.');
        } else {
            $request->validate([
                'improvement_id' => ['required', 'integer'],
                'action' => ['required', 'string'],
                'how_to' => ['required', 'string'],
                'whenn' => ['nullable', 'date'],
            ]);

            $attrs = [
                'improvement_id' => $improvementId,
                'action' => $request->string('action')->toString(),
                'how_to' => $request->string('how_to')->toString(),
                'responsible_id' => $request->integer('responsible_id') ?: null,
                'whenn' => $request->input('whenn') ?: null,
                'user_id' => auth()->id(),
            ];

            if ($id) {
                ImprovementActivity::findOrFail($id)->update($attrs);
                $this->hxNotify('Actividad actualizada.');
            } else {
                ImprovementActivity::create($attrs);
                $this->hxNotify('Actividad creada.');
            }
        }

        $this->hxRefresh(['#tab-content']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }

    public function destroy(int $activityId): JsonResponse
    {
        ImprovementActivity::findOrFail($activityId)->delete();

        $this->hxNotify('Actividad eliminada.');
        $this->hxRefresh(['#tab-content']);

        return $this->hxResponse();
    }

    public function asData(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', 15);

        $paginator = ImprovementActivity::query()
            ->where('improvement_id', $id)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        $items = $paginator->map(function (ImprovementActivity $a): array {
            $results = '';
            if (is_array($a->results)) {
                foreach ($a->results as $r) {
                    if (is_array($r)) {
                        $results .= "<div class=\"text-[10px]\">{$r[0]}: {$r[1]}</div>";
                    }
                }
            }

            $editRoute = route('improvement.activities.edit', [$a->improvement_id, $a->id]);
            $closeRoute = route('improvement.activities.close', [$a->improvement_id, $a->id]);
            $deleteRoute = route('improvement.activities.delete', $a->id);

            $actions = "<button class=\"btn-icon\" hx-get=\"{$editRoute}\" hx-target=\"#modal-body-2\" hx-swap=\"innerHTML\"><i class=\"ri-edit-line\"></i></button> ";
            if (! $a->fulfill) {
                $actions .= "<button class=\"btn-icon text-green-600\" hx-get=\"{$closeRoute}\" hx-target=\"#modal-body-2\" hx-swap=\"innerHTML\"><i class=\"ri-checkbox-circle-line\"></i></button> ";
            }
            $actions .= "<button class=\"btn-icon text-red-500\" hx-delete=\"{$deleteRoute}\" hx-confirm=\"¿Eliminar esta actividad?\"><i class=\"ri-delete-bin-line\"></i></button>";

            return [
                'id' => $a->id,
                'action' => $a->action,
                'how_to' => $a->how_to,
                'whenn' => $a->whenn?->format('Y-m-d'),
                'done' => $a->done?->format('Y-m-d'),
                'results' => $results,
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
