<?php

declare(strict_types=1);

namespace App\Actions\Maintenance\Tabs;

use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Data\Shared\TaskCreate;
use App\Models\Asset;
use App\Models\Mnt;
use App\Models\MntItem;
use App\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class Tasks
{
    use AsAction;
    use HtmxOrchestrator;

    /** GET /maintenance/{id}/tasks/form → modal nivel 2 reutilizando new-modal */
    public function asForm(Request $request, int $id): Response
    {
        $config = new Config(
            title: "Nueva Tarea",
            subtitle: "Correctivo #{$id}",
            icon: "ri-add-circle-line",
            multipart: false,
            formFields: [
                new Field(name: 'complexity', label: 'Complejidad', required: true, options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'attends',    label: 'Atiende',     required: true, options: ['Internal' => 'Internal', 'External' => 'External', 'Other' => 'Other'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'duration',   label: 'Tiempo (Minutos)', type: 'number', required: true, width: FieldWidth::Half),
                new Field(name: 'notes',      label: 'Notas técnicos', type: 'textarea', placeholder: 'Describe el trabajo realizado...', required: true),
            ],
        );

        $this->hxModalHeader([
            'icon'     => $config->icon,
            'title'    => $config->title,
            'subtitle' => $config->subtitle,
        ], '-2');

        $this->hxModalWidth('35%', '-2');

        return $this->hxView(view('components.new-modal', [
            'route'      => "maintenance/{$id}/tasks",
            'config'     => $config,
            'target'     => '#tab-content',
            'closeEvent' => 'close-modal-2',
        ]));
    }

    public function asData(Request $request, int $id): JsonResponse
    {
        $rows = MntItem::query()
            ->select(['mnt_items.*', 'users.name as technician'])
            ->where('mnt_id', $id)
            ->leftJoin('users', 'mnt_items.user_id', '=', 'users.id')
            ->orderBy('mnt_items.created_at', 'asc')
            ->get();

        return response()->json([
            'data' => $rows->map(fn (MntItem $r): array => [
                'date'       => $r->created_at?->format('Y-m-d H:i'),
                'technician' => $r->technician, // @phpstan-ignore-line
                'complexity' => $r->complexity,
                'attends'    => $r->attends,
                'time'       => $r->duration,
                'notes'      => $r->notes,
            ])->values()->all(),
        ]);
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $ticket = Mnt::findOrFail($id);
        $data   = TaskCreate::from($request->all());

        MntItem::create([
            'mnt_id'     => $id,
            'user_id'    => Auth::id(),
            'complexity' => $data->complexity,
            'attends'    => $data->attends,
            'duration'   => $data->duration,
            'notes'      => $data->notes,
        ]);

        if ($ticket->status === 'Open') {
            $ticket->update([
                'status'      => 'Started',
                'started_at'  => now(),
                'assignee_id' => Auth::id(),
            ]);
        }

        $this->hxNotify('Tarea guardada');
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
