<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Models;

use Illuminate\Database\Eloquent\Model;

final class Die extends Model
{
    public $timestamps = false;

    protected $table = 'matrices';

    protected $fillable = [
        'geometry_shape',
        'company_id',
        'category_id',
        'b',
        'h',
        'e1',
        'e2',
        'clicks',
        'systema',
        'products',
    ];

    protected $casts = [
        'clicks'  => 'array',
        'systema' => 'array',
        'b'       => 'float',
        'h'       => 'float',
        'e1'      => 'float',
        'e2'      => 'float',
    ];
}
