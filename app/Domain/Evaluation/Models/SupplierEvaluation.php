<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupplierEvaluation extends Model
{
    #[\Override]
    protected $table = 'suppliers_evaluation';

    #[\Override]
    protected $fillable = [
        'user_id',
        'nit',
        'supplier',
        'kind',
        'answers',
    ];

    #[\Override]
    protected $casts = [
        'answers' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
