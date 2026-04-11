<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Actions;

use App\Domain\Users\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadSidebar
{
    use AsAction;

    /** @return array<int, array{label: string, icon: string, url: ?string, children: array<int, mixed>}> */
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

        /** @var array<int, array{label: string, icon: string, url: ?string, children: array<int, mixed>}> $result */
        $result = $items
            ->groupBy('title')
            ->map(function ($items, $title): ?array {
                $first = $items->first();
                if (! $first instanceof Permission) {
                    return null;
                }

                // ── CASO 1: Sin subtitle y sin link → item directo ────────────
                $allSimple = $items->every(fn ($i): bool => empty($i->subtitle) && empty($i->link));
                if ($allSimple) {
                    return [
                        'title' => (string) $title,
                        'icon' => (string) ($first->icon ?: '<i class="ri-circle-line mr-3 text-lg"></i>'),
                        'url' => (string) $first->url,
                        'children' => [],
                    ];
                }

                // ── CASO 2 y 3: Tiene subtitle → grupo desplegable ────────────
                $children = $items
                    ->groupBy('subtitle')
                    ->map(function ($links, $subtitle) {
                        if (empty($subtitle)) {
                            return $links->map(fn ($l): array => [
                                'title' => (string) ($l->link ?: $l->name),
                                'url' => (string) $l->url,
                                'children' => [],
                            ])->values()->toArray();
                        }

                        $hasLinks = $links->filter(fn ($l): bool => ! empty($l->link))->isNotEmpty();

                        if (! $hasLinks) {
                            return $links->map(fn ($l): array => [
                                'title' => (string) $subtitle,
                                'url' => (string) $l->url,
                                'children' => [],
                            ])->unique('title')->values()->toArray();
                        }

                        return [[
                            'title' => (string) $subtitle,
                            'url' => null,
                            'children' => $links->map(fn ($l): array => [
                                'title' => (string) ($l->link ?: $l->name),
                                'url' => (string) $l->url,
                            ])->values()->toArray(),
                        ]];
                    })
                    ->flatten(1)
                    ->values()
                    ->toArray();

                return [
                    'title' => (string) $title,
                    'icon' => (string) ($first->icon ?: '<i class="ri-circle-line mr-3 text-lg"></i>'),
                    'url' => null,
                    'children' => $children,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        return $result;
    }
}
