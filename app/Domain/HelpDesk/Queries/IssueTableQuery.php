<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Queries;

use App\Domain\HelpDesk\Models\Issue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class IssueTableQuery
{
    /** @var Builder<Issue> */
    public private(set) Builder $query;

    public function __construct()
    {
        $userId      = Auth::id();
        $raw         = Auth::user()?->permissions ?? [];
        $permissions = is_array($raw) ? $raw : (array) json_decode((string) $raw, true);
        $canViewAll  = ! empty(array_intersect([35, 44], $permissions));

        /** @var Builder<Issue> $query */
        $query = Issue::query()
            ->select('issues.*')
            ->selectRaw('COALESCE((SELECT SUM(duration_minutes) FROM issue_items WHERE issue_items.issue_id = issues.id), 0) AS time_sum')
            ->with(['reporter', 'assignee', 'asset']);

        if (! $canViewAll && $userId) {
            $query->where('issues.reporter_id', $userId);
        }

        $this->query = $query;
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     */
    public function apply(array $filters, array $sorts): self
    {
        return $this->filter($filters)->sort($sorts);
    }

    /** @param array<string, mixed> $filters */
    private function filter(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) {
                continue;
            }

            $valStr = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id'          => $this->query->where('issues.id', (int) $value),
                'status'      => $this->query->where('issues.status', $valStr),
                'priority'    => $this->query->where('issues.priority', $valStr),
                'kind'        => $this->query->where('issues.kind', $valStr),
                'facility'    => $this->query->where('issues.facility', 'ilike', '%'.$valStr.'%'),
                'description' => $this->query->where('issues.description', 'ilike', '%'.$valStr.'%'),
                'sgc'         => $this->query->where('issues.sgc_code', 'ilike', '%'.$valStr.'%'),
                'cause'       => $this->query->where('issues.root_cause', 'ilike', '%'.$valStr.'%'),
                'complexity'  => $this->query->where('issues.complexity', $valStr),
                'created_at'  => $this->applyDateFilter($valStr),
                'search'      => $this->query->where(function (Builder $q) use ($valStr) {
                    $q->where('issues.description', 'ilike', '%'.$valStr.'%')
                        ->orWhere('issues.facility', 'ilike', '%'.$valStr.'%')
                        ->orWhere('issues.sgc_code', 'ilike', '%'.$valStr.'%')
                        ->orWhere('issues.kind', 'ilike', '%'.$valStr.'%')
                        ->orWhereHas('reporter', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%'))
                        ->orWhereHas('assignee', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%'))
                        ->orWhereHas('asset', fn (Builder $q) => $q->where('hostname', 'ilike', '%'.$valStr.'%')->orWhere('serial', 'ilike', '%'.$valStr.'%')->orWhere('sap', 'ilike', '%'.$valStr.'%'))
                        ->orWhereRaw('CAST(issues.id AS TEXT) LIKE ?', ['%'.$valStr.'%']);
                }),
                'reporter' => $this->query->whereHas('reporter', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%')),
                'assignee' => $this->query->whereHas('assignee', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%')),
                'asset'    => $this->query->whereHas('asset', fn (Builder $q) => $q->where('hostname', 'ilike', '%'.$valStr.'%')->orWhere('serial', 'ilike', '%'.$valStr.'%')->orWhere('sap', 'ilike', '%'.$valStr.'%')),
                default    => $this->query->where('issues.'.(string) $field, 'ilike', '%'.$valStr.'%'),
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->orderByRaw("
                CASE issues.status
                    WHEN 'Open'     THEN 1
                    WHEN 'Started'  THEN 2
                    WHEN 'Attended' THEN 3
                    WHEN 'Closed'   THEN 4
                    WHEN 'Rated'    THEN 5
                    WHEN 'Rejected' THEN 6
                    ELSE 7
                END ASC
            ")->orderBy('issues.created_at', 'DESC');

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy('issues.'.(string) $field, $dir);
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Issue> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Issue> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(string $value): void
    {
        if (str_contains($value, ' to ')) {
            [$from, $to] = explode(' to ', $value);
            $this->query->whereBetween('issues.created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } else {
            $this->query->whereDate('issues.created_at', $value);
        }
    }
}
