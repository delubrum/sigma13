<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $issue_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $notes
 * @property string|null $action_taken
 * @property string|null $complexity
 * @property int|null $duration_minutes
 * @property string|null $attendant_name
 */
#[Fillable([
    'issue_id', 'user_id',
    'notes', 'action_taken', 'complexity', 'duration_minutes', 'attendant_name',
])]
class IssueItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const UPDATED_AT = null;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at'       => 'datetime',
            'duration_minutes' => 'integer',
            'issue_id'         => 'integer',
            'user_id'          => 'integer',
        ];
    }

    /** @return BelongsTo<Issue, $this> */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidence')
            ->useDisk('r2')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/heic']);
    }
}
