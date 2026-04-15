<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $jp_id
 * @property int $user_id
 * @property string $kind
 * @property array|null $content
 */
#[Fillable(['jp_id', 'user_id', 'kind', 'content'])]
final class JobProfileItem extends Model
{
    #[\Override]
    protected $table = 'job_profile_items';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}
