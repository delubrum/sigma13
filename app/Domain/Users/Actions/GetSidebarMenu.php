<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Contracts\SidebarProviderContract;
use App\Domain\Shared\Models\Permission;
use Illuminate\Contracts\Auth\Authenticatable;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetSidebarMenu implements SidebarProviderContract
{
    use AsAction;

    /**
     * Implementation of the sidebar menu provider using the Permission model.
     * Reuses the original logic but encapsulated within the Users domain.
     */
    public function handle(Authenticatable $user): array
    {
        return $this->getMenuItemsForUser($user);
    }

    #[\Override]
    public function getMenuItemsForUser(Authenticatable $user): array
    {
        $items = Permission::query()
            ->where('kind', 'menu')
            ->whereNotNull('title')
            ->forUser($user)
            ->ordered()
            ->get();

        /** @var array<int, array{title: string, icon: string, url: ?string, children: array<int, array{title: string, url: string}>}> $result */
        $result = $items
            ->groupBy('title')
            ->map(function ($items, $title): ?array {
                $first = $items->first();
                if (! $first instanceof Permission) {
                    return null;
                }

                $allSimple = $items->every(fn ($i): bool => empty($i->subtitle) && empty($i->link));
                if ($allSimple) {
                    return [
                        'title' => (string) $title,
                        'icon' => (string) ($first->icon ?: '<i class="ri-circle-line mr-3 text-lg"></i>'),
                        'url' => (string) $first->url,
                        'children' => [],
                    ];
                }

                $children = $items
                    ->groupBy('subtitle')
                    ->map(function ($links, $subtitle) {
                        if (empty($subtitle)) {
                            return $links->map(fn ($l): array => [
                                'title' => (string) ($l->link ?: $l->name),
                                'url' => (string) $l->url,
                            ])->values()->toArray();
                        }

                        $hasLinks = $links->filter(fn ($l): bool => ! empty($l->link))->isNotEmpty();

                        if (! $hasLinks) {
                            return $links->map(fn ($l): array => [
                                'title' => (string) $subtitle,
                                'url' => (string) $l->url,
                            ])->unique('title')->values()->toArray();
                        }

                        return $links->map(fn ($l): array => [
                            'title' => (string) ($l->link ?: $l->name),
                            'url' => (string) $l->url,
                        ])->values()->toArray();
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
