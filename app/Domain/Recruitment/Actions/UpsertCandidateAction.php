<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\RecruitmentCandidate;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpsertCandidateAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     * @return array{candidate: RecruitmentCandidate, duplicate: bool}
     */
    public function handle(array $data): array
    {
        $candidateId = (int) ($data['id'] ?? 0);

        if ($candidateId > 0) {
            /** @var RecruitmentCandidate $candidate */
            $candidate = RecruitmentCandidate::query()->findOrFail($candidateId);
            unset($data['id'], $data['_token']);
            $candidate->fill($data);
            $candidate->save();

            return ['candidate' => $candidate, 'duplicate' => false];
        }

        $cc = (string) ($data['cc'] ?? '');
        if ($cc !== '' && DB::table('recruitment_candidates')->where('cc', $cc)->exists()) {
            $dummy = new RecruitmentCandidate;

            return ['candidate' => $dummy, 'duplicate' => true];
        }

        unset($data['_token']);
        $data['user_id'] ??= auth()->id();
        $data['status'] ??= 'appointment';

        /** @var RecruitmentCandidate $candidate */
        $candidate = RecruitmentCandidate::query()->create($data);

        return ['candidate' => $candidate, 'duplicate' => false];
    }
}
