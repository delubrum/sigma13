<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $mntp_id
 * @property int|null $user_id
 * @property string|null $complexity
 * @property string|null $attends
 * @property float|null $duration
 * @property string|null $notes
 * @property Carbon $created_at
 * @property-read User|null $technician
 * @property-read MntPreventive|null $form
 */
final class MntPItem extends Model
{
    protected $table = 'mntp_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'duration'   => 'float',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<MntPreventive, $this> */
    public function form(): BelongsTo
    {
        return $this->belongsTo(MntPreventive::class, 'mntp_id');
    }
}
