<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int|null $responsible_id
 * @property string|null $process
 * @property string|null $perspective
 * @property string|null $type
 * @property string|null $source
 * @property string|null $source_other
 * @property string|null $description
 * @property string|null $immediate_action
 * @property string $status
 * @property string|null $aim
 * @property string|null $goal
 * @property array<int,int>|null $user_ids
 * @property bool|null $repeated
 * @property string|null $cdate
 * @property string|null $notes
 * @property string|null $convenience
 * @property string|null $adequacy
 * @property string|null $effectiveness
 * @property string|null $rejection_reason
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 */
#[Fillable([
    'code', 'user_id', 'responsible_id', 'process', 'perspective',
    'type', 'source', 'source_other', 'description', 'immediate_action',
    'status', 'aim', 'goal', 'user_ids', 'repeated',
    'cdate', 'notes', 'convenience', 'adequacy', 'effectiveness',
    'rejection_reason', 'closed_at',
])]
final class Improvement extends Model
{
    #[\Override]
    protected $table = 'improvements';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'user_ids' => 'array',
            'created_at' => 'datetime',
            'closed_at' => 'datetime',
            'repeated' => 'boolean',
        ];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (self $improvement): void {
            if (auth()->check()) {
                $improvement->user_id ??= (int) auth()->id();
            }
            $improvement->status ??= 'Analysis';
            $improvement->created_at ??= now();
        });
    }

    /** @return HasMany<ImprovementCause, $this> */
    public function causes(): HasMany
    {
        return $this->hasMany(ImprovementCause::class, 'improvement_id');
    }

    /** @return HasMany<ImprovementActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(ImprovementActivity::class, 'improvement_id');
    }
}
