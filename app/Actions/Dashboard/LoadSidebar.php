<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadSidebar
{
    use AsAction;

    public function handle(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $items = Permission::query()
            ->where('kind', 'menu')
            ->whereNotNull('title')
            ->forUser($user)
            ->ordered()
            ->get();

        return $items
            ->groupBy('title')
            ->map(function ($items, $title): array {
                $first = $items->first();

                // ── CASO 1: Sin subtitle y sin link → item directo ────────────
                // Ej: Admin Desk, Inspections, Development, Print WO
                $allSimple = $items->every(fn ($i): bool => empty($i->subtitle) && empty($i->link));
                if ($allSimple) {
                    return [
                        'title' => $title,
                        'icon' => $first->icon ?: '<i class="ri-circle-line mr-3 text-lg"></i>',
                        'url' => $first->url,
                        'children' => [],
                    ];
                }

                // ── CASO 2 y 3: Tiene subtitle → grupo desplegable ────────────
                $children = $items
                    ->groupBy('subtitle')
                    ->map(function ($links, $subtitle) {
                        // Sin subtitle → hijos directos del grupo (sin sub-nivel)
                        // Ej: CBM, Assets, Documents bajo Infrastructure
                        if (empty($subtitle)) {
                            return $links->map(fn ($l): array => [
                                'title' => $l->link ?: $l->name,
                                'url' => $l->url,
                                'children' => [],
                            ])->values()->toArray();
                        }

                        // Con subtitle pero sin link → hijo directo con subtitle como label
                        $hasLinks = $links->filter(fn ($l): bool => ! empty($l->link))->isNotEmpty();

                        if (! $hasLinks) {
                            // CASO 2: subtitle es el label, url directo
                            return $links->map(fn ($l): array => [
                                'title' => $subtitle,
                                'url' => $l->url,
                                'children' => [],
                            ])->unique('title')->values()->toArray();
                        }

                        // CASO 3: subtitle agrupa links → sub-nivel desplegable
                        // Ej: Infrastructure > IT > Service Desk, Preventive
                        return [[
                            'title' => $subtitle,
                            'url' => null,
                            'children' => $links->map(fn ($l): array => [
                                'title' => $l->link ?: $l->name,
                                'url' => $l->url,
                            ])->values()->toArray(),
                        ]];
                    })
                    ->flatten(1)
                    ->values()
                    ->toArray();

                return [
                    'title' => $title,
                    'icon' => $first->icon ?: '<i class="ri-circle-line mr-3 text-lg"></i>',
                    'url' => null,
                    'children' => $children,
                ];
            })
            ->values()
            ->toArray();
    }
}
