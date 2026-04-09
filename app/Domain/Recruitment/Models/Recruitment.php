<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recruitment extends Model
{
    #[\Override]
    protected $table = 'recruitment';

    #[\Override]
    public $timestamps = false; // Only created_at exists, we will handle it manually or let DB handle it.

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = [
        'created_at' => 'datetime',
        'start_date' => 'date',
        'closed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'resources' => 'array',
    ];

    /** @return BelongsTo<User, $this> */
    public function requestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
