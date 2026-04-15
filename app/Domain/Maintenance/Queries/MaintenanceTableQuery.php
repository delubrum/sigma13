<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Queries;

use App\Domain\Maintenance\Models\Maintenance;
use Illuminate\Database\Eloquent\Builder;

final class MaintenanceTableQuery
{
    /** @param array<string, mixed> $filters */
    public static function build(array $filters = [], int|null $userId = null, array $permissions = []): Builder
    {
        // Equivalent to legacy joins and SELECT
        $query = Maintenance::query()
            ->select('mnt.*')
            ->selectRaw('COALESCE((SELECT SUM(duration) FROM mnt_items WHERE mnt_items.mnt_id = mnt.id), 0) as time_sum')
            ->with(['user', 'assignee', 'asset'])
            ->where('kind', 'Machinery');

        $canViewAll = ! empty(array_intersect([35, 44], $permissions));
        
        if (! $canViewAll && $userId) {
            $query->where('mnt.user_id', $userId);
        }

        // Apply Tabulator Filters
        if (! empty($filters) && is_array($filters)) {
            foreach ($filters as $f) {
                $field = $f['field'] ?? '';
                $value = $f['value'] ?? '';

                if (empty($field) || empty($value)) {
                    continue;
                }

                $mapped = self::mapField($field);

                if ($field === 'created_at') {
                    if (str_contains((string) $value, ' to ')) {
                        [$from, $to] = explode(' to ', (string) $value);
                        $query->whereBetween('mnt.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    } else {
                        $query->where('mnt.created_at', 'ILIKE', "%{$value}%");
                    }
                } elseif ($mapped) {
                    if (str_contains($mapped, '.')) {
                        // For relationship searches
                        [$rel, $col] = explode('.', $mapped);
                        $query->whereHas($rel, function ($q) use ($col, $value) {
                            $q->where($col, 'ILIKE', "%{$value}%");
                        });
                    } else {
                        if ($field === 'status') {
                            $query->where('mnt.status', $value);
                        } else {
                            $query->where($mapped, 'ILIKE', "%{$value}%");
                        }
                    }
                }
            }
        }

        // Order Hierarchy (Postgres)
        $query->orderByRaw("
            CASE mnt.status 
                WHEN 'Open' THEN 1 
                WHEN 'Started' THEN 2 
                WHEN 'Attended' THEN 3 
                WHEN 'Closed' THEN 4 
                WHEN 'Rated' THEN 5 
                WHEN 'Rejected' THEN 6 
                ELSE 7 
            END ASC
        ")->orderBy('mnt.created_at', 'DESC');

        return $query;
    }

    private static function mapField(string $field): ?string
    {
        return match ($field) {
            'id' => 'mnt.id',
            'user' => 'user.username',
            'facility' => 'mnt.facility',
            'priority' => 'mnt.priority',
            'description' => 'mnt.description',
            'assignee' => 'assignee.username',
            'status' => 'mnt.status',
            'sgc' => 'mnt.sgc',
            'cause' => 'mnt.root_cause',
            'rating' => 'mnt.rating',
            default => null,
        };
    }
}
