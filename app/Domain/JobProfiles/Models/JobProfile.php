<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property string $name
 * @property int|null $division_id
 * @property int|null $reports_to
 * @property array|null $reports
 * @property string|null $work_mode
 * @property string|null $rank
 * @property string|null $schedule
 * @property string|null $travel
 * @property string|null $relocation
 * @property string|null $experience
 * @property string|null $obs
 * @property string|null $lang
 * @property string|null $mission
 * @property string $status
 * @property Carbon|null $created_at
 */
#[Fillable([
    'code', 'user_id', 'name', 'division_id', 'reports_to', 'reports',
    'work_mode', 'rank', 'schedule', 'travel', 'relocation',
    'experience', 'obs', 'lang', 'mission', 'status',
])]
final class JobProfile extends Model
{
    #[\Override]
    protected $table = 'job_profiles';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'reports' => 'array',
            'created_at' => 'datetime',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(static function (JobProfile $model): void {
            $model->status ??= 'open';
            $model->created_at ??= now();
        });
    }
}
