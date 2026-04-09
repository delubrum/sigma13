<?php

declare(strict_types=1);

namespace App\Domain\IT\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $it_id
 * @property int|null $user_id
 * @property string|null $complexity
 * @property string|null $attends
 * @property float|null $duration
 * @property string|null $notes
 * @property Carbon $created_at
 * @property-read User|null $technician
 * @property-read string|null $technician
 * @property-read It|null $ticket
 */
final class ItItem extends Model
{
    #[\Override]
    protected $table = 'it_items';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'duration' => 'float',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<It, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(It::class, 'it_id');
    }
}
