<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Actions;

use App\Domain\HelpDesk\Data\SidebarData;
use App\Domain\HelpDesk\Models\Issue;
use App\Domain\Shared\Data\SidebarItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetIssueSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $issue = Issue::with(['reporter', 'assignee', 'asset', 'media'])->findOrFail($id);

        $hasAccess  = Gate::any(['35', '44']);
        $terminal   = in_array($issue->status, ['Closed', 'Rejected', 'Rated'], true);
        $isAssignee = $issue->assignee_id === null || $issue->assignee_id === (int) Auth::id();
        $canEdit    = $hasAccess && ! $terminal;
        $canClose   = $hasAccess && $isAssignee && in_array($issue->status, ['Open', 'Started', 'Attended'], true);

        $assets = $canEdit
            ? DB::table('assets')
                ->select('id', 'hostname', 'serial', 'sap')
                ->orderBy('hostname')
                ->get()
                ->map(fn (object $a): array => [
                    'id'    => $a->id,
                    'label' => mb_convert_case(
                        implode(' | ', array_filter([$a->hostname, $a->serial, $a->sap])),
                        MB_CASE_TITLE, 'UTF-8'
                    ),
                ])->all()
            : [];

        $technicians = $canEdit
            ? DB::table('users')
                ->select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn (object $u): array => ['id' => $u->id, 'name' => $u->name])
                ->all()
            : [];

        $expiry = now()->addMinutes(30);
        $media  = $issue->getFirstMedia('photos');
        $photoItem = $media
            ? new SidebarItem(
                label:    'Foto',
                value:    'Ver evidencia',
                icon:     'ri-image-line',
                url:      $media->getTemporaryUrl($expiry),
                linkIcon: 'ri-external-link-line',
            )
            : null;

        $color = match (strtolower($issue->status ?? 'open')) {
            'open'               => 'gray',
            'started'            => 'yellow',
            'attended', 'closed' => 'purple',
            'rated'              => 'green',
            'rejected'           => 'red',
            default              => 'gray',
        };

        $days = (string) (int) ($issue->created_at?->diffInDays($issue->closed_at ?? now()) ?? 0);

        return new SidebarData(
            id:          $issue->id,
            title:       $issue->asset?->hostname ?? 'Ticket #'.$issue->id,
            subtitle:    match (strtolower($issue->status ?? 'open')) {
                'open'     => 'Abierto',
                'started'  => 'Iniciado',
                'attended' => 'Atendido',
                'closed'   => 'Cerrado',
                'rejected' => 'Rechazado',
                'rated'    => 'Valorado',
                default    => $issue->status ?? 'Abierto',
            },
            color:       $color,
            properties:  array_values(array_filter([
                new SidebarItem(label: 'Usuario',  value: $issue->reporter?->name ?? '—',                    icon: 'ri-user-line'),
                new SidebarItem(label: 'Fecha',    value: $issue->created_at?->format('d/m/Y H:i') ?? '—',   icon: 'ri-calendar-line'),
                new SidebarItem(label: 'Sede',     value: $issue->facility,                                   icon: 'ri-building-line'),
                new SidebarItem(label: 'Iniciado',  value: $issue->started_at?->format('d/m/Y H:i') ?? '—',  icon: 'ri-play-line'),
                new SidebarItem(label: 'Atendido', value: $issue->ended_at?->format('d/m/Y H:i') ?? '—',     icon: 'ri-check-line'),
                new SidebarItem(label: 'Cerrado',  value: $issue->closed_at?->format('d/m/Y H:i') ?? '—',    icon: 'ri-checkbox-circle-line'),
                new SidebarItem(label: 'Días',     value: $days,                                              icon: 'ri-timer-line'),
                new SidebarItem(label: 'Rating',   value: $issue->rating ? (string) $issue->rating : '—',    icon: 'ri-star-line'),
                $photoItem,
            ])),
            description:      $issue->description,
            canEdit:          $canEdit,
            canClose:         $canClose,
            assetId:          $issue->asset_id,
            assetLabel:       $issue->asset
                ? mb_convert_case(
                    implode(' | ', array_filter([$issue->asset->hostname, $issue->asset->serial, $issue->asset->sap])),
                    MB_CASE_TITLE, 'UTF-8'
                )
                : '',
            assigneeId:       $issue->assignee_id,
            assigneeName:     $issue->assignee?->name ?? '',
            priority:         $issue->priority ?? '',
            sgcCode:          $issue->sgc_code ?? '',
            rootCause:        $issue->root_cause ?? '',
            assets:       $assets,
            technicians:  $technicians,
        );
    }
}
