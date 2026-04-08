<?php

declare(strict_types=1);

namespace App\Actions\IT\Tabs;

use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Data\Shared\TaskCreate;
use App\Models\Asset;
use App\Models\It;
use App\Models\ItItem;
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

    /** GET /it/{id}/tasks/form → modal nivel 2 reutilizando new-modal */
    public function asForm(Request $request, int $id): Response
    {
        $config = new Config(
            title: "Nueva Tarea",
            subtitle: "Ticket #{$id}",
            icon: "ri-add-circle-line",
            multipart: false,
            formFields: [
                new Field(name: 'complexity', label: 'Complejidad', required: true, options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'attends',    label: 'Atiende',     required: true, options: ['Internal' => 'Internal', 'External' => 'External', 'Other' => 'Other'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'duration',   label: 'Tiempo (Minutos)', type: 'number', required: true, width: FieldWidth::Half),
                new Field(name: 'notes',      label: 'Notas de trabajo', type: 'textarea', placeholder: 'Describe lo realizado...', required: true),
            ],
        );

        $this->hxModalHeader([
            'icon'     => $config->icon,
            'title'    => $config->title,
            'subtitle' => $config->subtitle,
        ], '-2');

        $this->hxModalWidth('35%', '-2');

        return $this->hxView(view('components.new-modal', [
            'route'  => "it/{$id}/tasks",
            'config' => $config,
            'target' => '#tab-content',
            'closeEvent' => 'close-modal-2',
        ]));
    }

    /** GET /it/{id}/tasks → Tabulator data */
    public function asData(Request $request, int $id): JsonResponse
    {
        $rows = ItItem::query()
            ->select(['it_items.*', 'users.name as technician'])
            ->where('it_id', $id)
            ->leftJoin('users', 'it_items.user_id', '=', 'users.id')
            ->orderBy('it_items.created_at', 'asc')
            ->get();

        return response()->json([
            'data' => $rows->map(fn (ItItem $r): array => [
                'date'       => $r->created_at?->format('Y-m-d H:i'),
                'technician' => $r->technician,  // @phpstan-ignore-line
                'complexity' => $r->complexity,
                'attends'    => $r->attends,
                'time'       => $r->duration,
                'notes'      => $r->notes,
            ])->values()->all(),
        ]);
    }

    /** POST /it/{id}/tasks → guardar tarea */
    public function asStore(Request $request, int $id): JsonResponse
    {
        $ticket = It::findOrFail($id);
        $data   = TaskCreate::from($request->all());

        ItItem::create([
            'it_id'      => $id,
            'user_id'    => Auth::id(),
            'complexity' => $data->complexity,
            'attends'    => $data->attends,
            'duration'   => $data->duration,
            'notes'      => $data->notes,
        ]);

        // Auto-start on first task
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
