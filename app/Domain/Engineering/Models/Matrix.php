<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;

final class Matrix extends Model
{
    protected $table = 'matrices';

    protected $guarded = [];

    protected $casts = [
        'clicks' => 'array',
        'systema' => 'array',
    ];
}
