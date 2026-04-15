<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Actions;

use App\Domain\Fasteners\Data\SidebarData;
use App\Domain\Fasteners\Models\Fastener;
use App\Domain\Shared\Data\SidebarItem;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFastenerSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $f = Fastener::findOrFail($id);

        $img = "uploads/screws/{$f->code}/{$f->code}.png";
        $imgUrl = file_exists(public_path($img)) ? asset($img) . '?v=' . filemtime(public_path($img)) : null;

        return new SidebarData(
            id: $f->id,
            title: $f->code ?? 'N/A',
            subtitle: $f->category ?? 'General',
            color: 'blue',
            properties: [
                new SidebarItem(label: 'Head', value: $f->head ?? '—'),
                new SidebarItem(label: 'Screwdriver', value: $f->screwdriver ?? '—'),
                new SidebarItem(label: 'Diameter', value: $f->diameter ?? '—'),
                new SidebarItem(label: 'Length', value: $f->item_length ?? '—'),
            ],
            imgUrl: $imgUrl,
            model: $f
        );
    }
}
