<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $mnt_id
 * @property int|null $user_id
 * @property string|null $complexity
 * @property string|null $attends
 * @property float|null $duration
 * @property string|null $notes
 * @property Carbon $created_at
 * @property-read User|null $technician
 * @property-read string|null $technician_name
 * @property-read Mnt|null $ticket
 */
final class MntItem extends Model
{
    #[\Override]
    protected $table = 'mnt_items';

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

    /** @return BelongsTo<Mnt, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Mnt::class, 'mnt_id');
    }
}
