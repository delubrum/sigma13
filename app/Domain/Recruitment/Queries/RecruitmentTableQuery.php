<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Queries;

use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class RecruitmentTableQuery
{
    /** @var Builder<Recruitment> */
    public private(set) Builder $query;

    public function __construct()
    {
        $hiredSub    = "COALESCE((SELECT COUNT(id) FROM recruitment_candidates WHERE recruitment_id = recruitment.id AND status = 'hired'),0)";
        $convSub     = "CASE WHEN recruitment.qty > 0 THEN ROUND(($hiredSub::numeric / recruitment.qty) * 100) ELSE 0 END";
        $profileSub  = '(SELECT name FROM job_profiles WHERE id = recruitment.profile_id LIMIT 1)';
        $divisionSub = '(SELECT name FROM hr_db WHERE id = (SELECT division_id FROM job_profiles WHERE id = recruitment.profile_id LIMIT 1) LIMIT 1)';
        $areaSub     = '(SELECT area FROM hr_db WHERE id = (SELECT division_id FROM job_profiles WHERE id = recruitment.profile_id LIMIT 1) LIMIT 1)';
        $creatorSub  = '(SELECT username FROM users WHERE id = recruitment.user_id LIMIT 1)';
        $assigneeSub = '(SELECT username FROM users WHERE id = recruitment.assignee_id LIMIT 1)';
        $approverSub = '(SELECT username FROM users WHERE email = recruitment.approver LIMIT 1)';
        $daysSub     = "EXTRACT(DAY FROM (COALESCE(recruitment.closed_at, NOW()) - recruitment.created_at))::int";
        $hiredQtySub = "($hiredSub || '/' || recruitment.qty)";

        /** @var Builder<Recruitment> $q */
        $q = Recruitment::query()->select(
            'recruitment.*',
            DB::raw("$creatorSub  AS creator_name"),
            DB::raw("$assigneeSub AS assignee_name"),
            DB::raw("COALESCE($approverSub, recruitment.approver) AS approver_name"),
            DB::raw("$profileSub  AS profile_name"),
            DB::raw("$divisionSub AS division_name"),
            DB::raw("$areaSub     AS area_name"),
            DB::raw("$convSub     AS conversion_pct"),
            DB::raw("$hiredQtySub AS hired_qty"),
            DB::raw("$daysSub     AS days_open"),
        );

        $this->query = $q;
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string> $sorts
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

            $v = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id'       => $this->query->where('recruitment.id', is_numeric($v) ? (int) $v : 0),
                'date'     => $this->applyDateFilter($v, 'recruitment.created_at'),
                'status'   => $this->query->where('recruitment.status', $v),
                'profile'  => $this->query->whereRaw('(SELECT name FROM job_profiles WHERE id = recruitment.profile_id LIMIT 1) ILIKE ?', ["%$v%"]),
                'division' => $this->query->whereRaw('(SELECT name FROM hr_db WHERE id = (SELECT division_id FROM job_profiles WHERE id = recruitment.profile_id LIMIT 1) LIMIT 1) ILIKE ?', ["%$v%"]),
                'creator'  => $this->query->whereRaw('(SELECT username FROM users WHERE id = recruitment.user_id LIMIT 1) ILIKE ?', ["%$v%"]),
                'assignee' => $this->query->whereRaw('(SELECT username FROM users WHERE id = recruitment.assignee_id LIMIT 1) ILIKE ?', ["%$v%"]),
                default    => null,
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->orderBy('recruitment.id', 'desc');

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            match ((string) $field) {
                'date'    => $this->query->orderBy('recruitment.created_at', $dir),
                'status'  => $this->query->orderBy('recruitment.status', $dir),
                default   => $this->query->orderBy('recruitment.id', $dir),
            };
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Recruitment> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Recruitment> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(string $value, string $column): void
    {
        if (str_contains($value, ' to ')) {
            $this->query->whereBetween($column, explode(' to ', $value));
        } else {
            $this->query->whereDate($column, $value);
        }
    }
}
