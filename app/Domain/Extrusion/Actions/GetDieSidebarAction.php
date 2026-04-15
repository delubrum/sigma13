<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Actions;

use App\Domain\Extrusion\Data\SidebarData;
use App\Domain\Extrusion\Models\Die;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetDieSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        /** @var Die $die */
        $die = Die::query()->findOrFail($id);

        $shape = $die->geometry_shape;

        // Files from storage
        $files = [];
        $disk  = Storage::disk('public');
        $dir   = "matrices/{$shape}";
        if ($disk->exists($dir)) {
            foreach ($disk->files($dir) as $path) {
                $name = basename($path);
                if (str_ends_with(strtolower($name), '.png')) {
                    continue;
                }
                $files[] = [
                    'name' => $name,
                    'url'  => $disk->url($path),
                ];
            }
        }

        // Option lists for dropdowns
        $allShapes = DB::table('matrices')
            ->distinct()->orderBy('geometry_shape')
            ->pluck('geometry_shape')
            ->filter(static fn ($v) => ! blank($v))
            ->values()->all();

        $allCompanies = DB::table('matrices')
            ->distinct()->orderBy('company_id')
            ->whereNotNull('company_id')->where('company_id', '!=', '')
            ->pluck('company_id')->values()->all();

        $allCategories = DB::table('matrices_db')
            ->where('kind', 'Category')->orderBy('name')
            ->pluck('name')->values()->all();

        $allSystems = DB::table('matrices_db')
            ->where('kind', 'System')->orderBy('name')
            ->pluck('name')->values()->all();

        return new SidebarData(
            id:            $die->id,
            geometry_shape: $shape,
            company_id:    (string) ($die->company_id ?? ''),
            category_id:   (string) ($die->category_id ?? ''),
            b:             (string) ($die->b ?? ''),
            h:             (string) ($die->h ?? ''),
            e1:            (string) ($die->e1 ?? ''),
            e2:            (string) ($die->e2 ?? ''),
            clicks:        $die->clicks ?? [],
            systema:       $die->systema ?? [],
            files:         $files,
            allShapes:     $allShapes,
            allCategories: $allCategories,
            allSystems:    $allSystems,
            allCompanies:  $allCompanies,
        );
    }
}
