<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Actions;

use App\Domain\Assets\Models\Asset;
use App\Domain\Tickets\Data\SidebarData;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Users\Models\User;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetTicketSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $ticket = Ticket::query()->with('user')->findOrFail($id);

        /** @var Collection<int, mixed> $assets */
        $assets = Asset::query()
            ->select('id', 'hostname', 'serial', 'sap')
            ->orderBy('hostname')
            ->get()
            ->map(fn ($a): array => [
                'id' => $a->id,
                'label' => mb_convert_case((string) $a->hostname, MB_CASE_TITLE, 'UTF-8')." | {$a->serial} | {$a->sap}",
            ]);

        /** @var Collection<int, mixed> $assignees */
        $assignees = User::query()
            ->where('is_active', true)
            ->where('permissions', 'LIKE', '%"35"%')
            ->orderBy('name')
            ->get(['id', 'name']);

        return SidebarData::fromModel($ticket, $assets, $assignees);
    }
}
