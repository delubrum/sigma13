<?php

declare(strict_types=1);

namespace App\Domain\Shared\Services;

use App\Contracts\HasOptions;
use App\Support\DomainResolver;
use Illuminate\Support\Facades\DB;

final class OptionsResolver
{
    /**
     * @param  array<string, mixed>  $params
     * @return list<array{value: mixed, label: string}>|array<int|string, string>
     */
    public static function resolve(string $route, array $params = []): array
    {
        // Global catalog table — db(id, kind, name, area)
        if ($route === 'global.options' && ($params['model'] ?? null) === 'db') {
            return self::db($params);
        }

        // Delegate to the owning module's IndexAdapter via HasOptions
        // Route format: "{module}.options" or "{module}.options.{key}"
        $parts  = explode('.', $route);
        $module = $parts[0];
        $key    = implode('.', array_slice($parts, 2)) ?: ($params['model'] ?? '');

        $domain     = DomainResolver::fromRoute($module);
        $indexClass = "App\\Domain\\{$domain}\\Web\\Adapters\\IndexAdapter";

        if (class_exists($indexClass)) {
            $instance = resolve($indexClass);

            if ($instance instanceof HasOptions) {
                return $instance->resolveOptions($key, $params);
            }
        }

        return [];
    }

    /** @param array<string, mixed> $params */
    private static function db(array $params): array
    {
        $query = DB::table('db')->orderBy('name');

        if (isset($params['kind'])) {
            $query->where('kind', $params['kind']);
        }

        if (isset($params['area'])) {
            $query->where('area', $params['area']);
        }

        return $query->get()
            ->map(fn (object $r): array => ['value' => $r->id, 'label' => $r->name])
            ->all();
    }
}
