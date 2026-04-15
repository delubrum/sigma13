<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $recruitment_id
 * @property int|null $user_id
 * @property int|null $recruiter_id
 * @property string|null $kind
 * @property string|null $name
 * @property string|null $cc
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $age
 * @property string|null $city
 * @property string|null $neighborhood
 * @property string|null $maritalstatus
 * @property string|null $liveswith
 * @property string|null $relativework
 * @property string|null $relatives_data
 * @property string|null $educationlevel
 * @property string|null $degree
 * @property string|null $school
 * @property string|null $work_experience
 * @property string|null $wage
 * @property string|null $has_knowledge
 * @property string|null $shortgoals
 * @property string|null $longgoals
 * @property string|null $reasons
 * @property string|null $talla_pantalon
 * @property string|null $talla_camisa
 * @property string|null $talla_zapatos
 * @property string|null $cv_source
 * @property string|null $psychometrics
 * @property string|null $disc_answers
 * @property string|null $disc_date
 * @property string|null $pf_answers
 * @property string|null $pf_date
 * @property string|null $appointment
 * @property string|null $appointment_mode
 * @property string|null $appointment_location
 * @property string|null $teams_link
 * @property string|null $additional_instructions
 * @property string|null $contract_email
 * @property string|null $status
 * @property string|null $hired
 * @property string|null $candidate_list
 * @property array<mixed>|null $resources
 * @property Carbon|null $status_at
 * @property Carbon|null $created_at
 * @property string|null $data_consent
 */
#[Fillable([
    'recruitment_id', 'user_id', 'recruiter_id', 'kind', 'name', 'cc', 'email',
    'phone', 'age', 'city', 'neighborhood', 'maritalstatus', 'liveswith',
    'relativework', 'relatives_data', 'educationlevel', 'degree', 'school',
    'work_experience', 'wage', 'has_knowledge', 'shortgoals', 'longgoals',
    'reasons', 'talla_pantalon', 'talla_camisa', 'talla_zapatos', 'cv_source',
    'psychometrics', 'disc_answers', 'disc_date', 'pf_answers', 'pf_date',
    'appointment', 'appointment_mode', 'appointment_location', 'teams_link',
    'additional_instructions', 'contract_email', 'status', 'hired',
    'candidate_list', 'resources', 'status_at', 'data_consent',
])]
final class RecruitmentCandidate extends Model
{
    #[\Override]
    protected $table = 'recruitment_candidates';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'resources' => 'array',
            'created_at' => 'datetime',
            'status_at' => 'datetime',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (self $candidate): void {
            $candidate->status ??= 'appointment';
            $candidate->created_at ??= now();
        });
    }

    /** @return BelongsTo<Recruitment, $this> */
    public function recruitment(): BelongsTo
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }
}
