<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\Recruitment;
use App\Domain\Recruitment\Models\RecruitmentCandidate;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateCandidateStatusAction
{
    use AsAction;

    private const array REQUIRED_FOR_HIRE = [
        'name', 'cc', 'email', 'phone', 'age', 'city', 'neighborhood',
        'maritalstatus', 'liveswith', 'relativework', 'educationlevel',
        'degree', 'school', 'work_experience', 'wage', 'has_knowledge',
        'shortgoals', 'longgoals', 'reasons', 'talla_pantalon',
        'talla_camisa', 'talla_zapatos',
    ];

    /**
     * @return array{ok: bool, missing: string[]}
     */
    public function handle(int $candidateId, string $field, string $value): array
    {
        /** @var RecruitmentCandidate $candidate */
        $candidate = RecruitmentCandidate::query()->findOrFail($candidateId);

        if ($field === 'hired' && in_array($value, ['screening', 'hired'], true)) {
            $missing = $this->validateHireFields($candidate);
            if ($missing !== []) {
                return ['ok' => false, 'missing' => $missing];
            }
        }

        $candidate->$field = $value;
        $candidate->status_at = now()->toDateTimeString();
        $candidate->save();

        if ($value === 'hired') {
            $this->checkAndCloseRecruitment($candidate->recruitment_id);
        }

        return ['ok' => true, 'missing' => []];
    }

    /** @return string[] */
    private function validateHireFields(RecruitmentCandidate $candidate): array
    {
        $missing = [];
        foreach (self::REQUIRED_FOR_HIRE as $col) {
            $val = $candidate->$col ?? null;
            if (blank($val) || $val === '[]' || $val === 'null') {
                $missing[] = $col;
            }
        }

        $psych = $candidate->psychometrics ?? '';
        if (in_array($psych, ['CISD', 'Both'], true) && blank($candidate->disc_answers)) {
            $missing[] = 'disc_answers';
        }
        if (in_array($psych, ['PF', 'Both'], true) && blank($candidate->pf_answers)) {
            $missing[] = 'pf_answers';
        }

        return $missing;
    }

    private function checkAndCloseRecruitment(int $recruitmentId): void
    {
        /** @var Recruitment|null $recruitment */
        $recruitment = Recruitment::query()->find($recruitmentId);
        if (! $recruitment) {
            return;
        }

        $hiredCount = RecruitmentCandidate::query()
            ->where('recruitment_id', $recruitmentId)
            ->where('status', 'hired')
            ->count();

        if ($hiredCount >= $recruitment->qty) {
            $recruitment->status = 'closed';
            $recruitment->closed_at = now();
            $recruitment->save();
        }
    }
}
