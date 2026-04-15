<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Queries;

use App\Domain\Recruitment\Models\RecruitmentCandidate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class CandidateTableQuery
{
    /** @var Builder<RecruitmentCandidate> */
    public private(set) Builder $query;

    public function __construct()
    {
        $recruiterSub = '(SELECT username FROM users WHERE id = recruitment_candidates.recruiter_id LIMIT 1)';

        /** @var Builder<RecruitmentCandidate> $q */
        $q = RecruitmentCandidate::query()->select(
            'recruitment_candidates.*',
            DB::raw("$recruiterSub AS recruiter_name"),
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
    public function apply(array $filters, array $sorts, int $recruitmentId): self
    {
        $this->query->where('recruitment_candidates.recruitment_id', $recruitmentId);

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
                'id' => $this->query->where('recruitment_candidates.id', is_numeric($v) ? (int) $v : 0),
                'status' => $this->query->where('recruitment_candidates.status', $v),
                'name' => $this->query->where('recruitment_candidates.name', 'ilike', "%$v%"),
                'email' => $this->query->where('recruitment_candidates.email', 'ilike', "%$v%"),
                'cc' => $this->query->where('recruitment_candidates.cc', 'ilike', "%$v%"),
                'kind' => $this->query->where('recruitment_candidates.kind', 'ilike', "%$v%"),
                default => null,
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->orderBy('recruitment_candidates.id', 'desc');

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy('recruitment_candidates.'.$field, $dir);
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, RecruitmentCandidate> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, RecruitmentCandidate> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }
}
