<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Data\SidebarData;
use App\Domain\Maintenance\Data\UpsertData;
use App\Domain\Maintenance\Models\Maintenance;
use App\Domain\Shared\Data\SidebarItem;
use App\Domain\Shared\Services\SchemaGenerator;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenanceSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $mnt = Maintenance::with(['user', 'asset'])->findOrFail($id);

        $color = match (strtolower($mnt->status)) {
            'open' => 'gray',
            'started' => 'yellow',
            'closed', 'attended' => 'purple',
            'rated' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };

        return new SidebarData(
            id: $mnt->id,
            title: $mnt->asset->hostname ?? 'Ticket #'.$mnt->id,
            subtitle: $mnt->status,
            color: $color,
            properties: [
                new SidebarItem(label: 'Priority', value: $mnt->priority ?? '—', icon: 'ri-flag-line'),
                new SidebarItem(label: 'Facility', value: $mnt->facility ?? '—', icon: 'ri-building-line'),
                new SidebarItem(label: 'Created By', value: $mnt->user->username ?? '—', icon: 'ri-user-line'),
                new SidebarItem(label: 'Days', value: (string) ($mnt->created_at?->diffInDays($mnt->closed_at ?? now()) ?? 0), icon: 'ri-timer-line'),
            ],
            model: $mnt,
            fields: SchemaGenerator::toFields(UpsertData::class)
        );
    }
}
