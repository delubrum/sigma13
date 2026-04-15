<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Models;

use Illuminate\Database\Eloquent\Model;

final class DieItem extends Model
{
    public $timestamps = false;

    protected $table = 'matrices_db';

    protected $fillable = [
        'name',
        'kind',
    ];
}
