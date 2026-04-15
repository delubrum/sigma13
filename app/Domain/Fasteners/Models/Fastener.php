<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Models;

use Illuminate\Database\Eloquent\Model;

final class Fastener extends Model
{
    protected $table = 'screws';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'category',
        'description',
        'head',
        'screwdriver',
        'diameter',
        'item_length',
        'observation',
    ];
}
