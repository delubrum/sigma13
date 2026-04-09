<?php

declare(strict_types=1);

namespace App\Domain\IT\Actions\Tabs;

use App\Domain\IT\Models\It;
use App\Domain\IT\Models\ItItem;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use App\Domain\Shared\Data\TaskCreate;
use App\Support\HtmxOrchestrator;
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
            title: 'Nueva Tarea',
            subtitle: "Ticket #{$id}",
            icon: 'ri-add-circle-line',
            formFields: [
                new Field(name: 'complexity', label: 'Complejidad', required: true, options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'attends', label: 'Atiende', required: true, options: ['Internal' => 'Internal', 'External' => 'External', 'Other' => 'Other'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'duration', label: 'Tiempo (Minutos)', type: 'number', required: true, width: FieldWidth::Half),
                new Field(name: 'notes', label: 'Notas de trabajo', type: 'textarea', required: true, placeholder: 'Describe lo realizado...'),
            ],
            multipart: false,
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ], '-2');

        $this->hxModalWidth('35%', '-2');

        return $this->hxView(view('components.new-modal', [
            'route' => "it/{$id}/tasks",
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
            ->oldest('it_items.created_at')
            ->get();

        return response()->json([
            'data' => $rows->map(fn (ItItem $r): array => [
                'date' => $r->created_at->format('Y-m-d H:i'),
                'technician' => $r->technician,
                'complexity' => $r->complexity,
                'attends' => $r->attends,
                'time' => $r->duration,
                'notes' => $r->notes,
            ])->values()->all(),
        ]);
    }

    /** POST /it/{id}/tasks → guardar tarea */
    public function asStore(Request $request, int $id): JsonResponse
    {
        $ticket = It::findOrFail($id);
        $data = TaskCreate::from($request->all());

        ItItem::create([
            'it_id' => $id,
            'user_id' => Auth::id(),
            'complexity' => $data->complexity,
            'attends' => $data->attends,
            'duration' => $data->duration,
            'notes' => $data->notes,
        ]);

        // Auto-start on first task
        if ($ticket->status === 'Open') {
            $ticket->update([
                'status' => 'Started',
                'started_at' => now(),
                'assignee_id' => Auth::id(),
            ]);
        }

        $this->hxNotify('Tarea guardada');
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
