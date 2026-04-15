<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Queries;

use App\Domain\JobProfiles\Models\JobProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class JobProfileTableQuery
{
    /** @var Builder<JobProfile> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<JobProfile> $q */
        $q = JobProfile::query()->select(
            'job_profiles.*',
            DB::raw('(SELECT name FROM hr_db WHERE id = job_profiles.reports_to LIMIT 1) as reports_to_name'),
            DB::raw('(SELECT name FROM hr_db WHERE id = job_profiles.division_id LIMIT 1) as division_name'),
            DB::raw('(SELECT area FROM hr_db WHERE id = job_profiles.division_id LIMIT 1) as area_name'),
        );
        $this->query = $q;
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
            $val = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id' => $this->query->where('job_profiles.id', 'ilike', "%{$val}%"),
                'code' => $this->query->where('job_profiles.code', 'ilike', "%{$val}%"),
                'name' => $this->query->where('job_profiles.name', 'ilike', "%{$val}%"),
                'division' => $this->query->whereRaw(
                    'EXISTS (SELECT 1 FROM hr_db WHERE id = job_profiles.division_id AND name ILIKE ?)',
                    ["%{$val}%"]
                ),
                'created_at' => str_contains($val, ' to ')
                    ? $this->filterDateRange('job_profiles.created_at', $val)
                    : $this->query->whereRaw('DATE(job_profiles.created_at) = ?', [$val]),
                'work_mode' => $this->query->where('job_profiles.work_mode', 'ilike', "%{$val}%"),
                'rank' => $this->query->where('job_profiles.rank', 'ilike', "%{$val}%"),
                default => null,
            };
        }

        return $this;
    }

    private function filterDateRange(string $column, string $value): void
    {
        [$from, $to] = explode(' to ', $value);
        $this->query->whereBetween(DB::raw("DATE({$column})"), [trim($from), trim($to)]);
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        foreach ($sorts as $field => $dir) {
            $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
            match ((string) $field) {
                'id' => $this->query->orderBy('job_profiles.id', $dir),
                'code' => $this->query->orderBy('job_profiles.code', $dir),
                'name' => $this->query->orderBy('job_profiles.name', $dir),
                'created_at' => $this->query->orderBy('job_profiles.created_at', $dir),
                default => null,
            };
        }

        if ($sorts === []) {
            $this->query->orderBy('job_profiles.id', 'desc');
        }

        return $this;
    }

    /** @return LengthAwarePaginator<JobProfile> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
