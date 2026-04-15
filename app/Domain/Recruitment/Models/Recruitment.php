<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $profile_id
 * @property int|null $assignee_id
 * @property string|null $approver
 * @property int $qty
 * @property int $complexity
 * @property string $status
 * @property string|null $city
 * @property string|null $contract
 * @property string|null $srange
 * @property string|null $start_date
 * @property string|null $cause
 * @property string|null $replaces
 * @property string|null $others
 * @property array<mixed>|null $resources
 * @property string|null $rejection
 * @property Carbon|null $approved_at
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 */
#[Fillable([
    'user_id', 'profile_id', 'assignee_id', 'approver', 'qty', 'complexity',
    'status', 'city', 'contract', 'srange', 'start_date', 'cause', 'replaces',
    'others', 'resources', 'rejection', 'approved_at', 'closed_at',
])]
final class Recruitment extends Model
{
    #[\Override]
    protected $table = 'recruitment';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'resources' => 'array',
            'created_at' => 'datetime',
            'approved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (self $recruitment): void {
            if (auth()->check()) {
                $recruitment->user_id ??= (int) auth()->id();
            }
            $recruitment->status ??= 'approval';
            $recruitment->complexity ??= 15;
            $recruitment->created_at ??= now();
        });
    }

    /** @return HasMany<RecruitmentCandidate, $this> */
    public function candidates(): HasMany
    {
        return $this->hasMany(RecruitmentCandidate::class, 'recruitment_id');
    }
}
